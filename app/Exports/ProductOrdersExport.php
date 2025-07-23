<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $shopId; // ID toko mitra
    protected $isGeneralAdminReport; // Menentukan apakah ini laporan Admin Umum (tanpa filter shop_id)

    public function __construct($startDate = null, $endDate = null, $shopId = null, $isGeneralAdminReport = false)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
        $this->shopId = $shopId;
        $this->isGeneralAdminReport = $isGeneralAdminReport;
    }

    /**
     * Mengambil koleksi data pembayaran pesanan produk yang akan diekspor.
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Payment::with([
            'user', // Customer
            'order.user', // User yang membuat order jika berbeda dari payment->user
            'order.shop.subdomain', // Toko dan subdomain dari order
            'order.items.product',
            'order.items.variant', // Termasuk modal_price
            'order.shipping' // Untuk info resi
        ])
        ->whereNotNull('order_id') // PENTING: Hanya pembayaran yang terkait dengan Order produk
        ->whereNull('subs_package_id'); // PENTING: Pastikan bukan pembayaran langganan

        // Filter berdasarkan shop_id jika ini laporan mitra (bukan admin umum)
        if ($this->shopId && !$this->isGeneralAdminReport) {
            $query->whereHas('order.shop', function($q) {
                $q->where('id', $this->shopId);
            });
        }

        // Filter berdasarkan rentang tanggal jika ada
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        } elseif ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        } elseif ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate);
        }

        return $query->latest('created_at')->get();
    }

    /**
     * Mendefinisikan header kolom untuk pembayaran pesanan produk.
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Pembayaran',
            'ID Order',
            'Tanggal Order',
            'Nama Toko',
            'Subdomain Toko',
            'Nama Pelanggan',
            'Email Pelanggan',
            'Status Pembayaran',
            'Metode Pembayaran',
            'Total Pembayaran Order (Rp)',
            'Total Modal Order (Rp)',
            'Keuntungan Bersih Order (Rp)',
            'Metode Pengiriman',
            'Layanan Pengiriman',
            'Nomor Resi',
            'ID Transaksi Midtrans',
            'Detail Items (Produk - Varian: Qty @ Harga)', // Contoh detail item
        ];
    }

    /**
     * Memetakan data pembayaran pesanan produk untuk setiap baris di Excel.
     * @param mixed $payment
     * @return array
     */
    public function map($payment): array
    {
        $order = $payment->order;
        $totalModal = 0;
        $netProfit = 0;
        $itemDetails = [];

        if ($order) {
            foreach ($order->items as $item) {
                $itemCost = 0;
                if ($item->variant && $item->variant->modal_price !== null) {
                    $itemCost = $item->variant->modal_price * $item->quantity;
                } else {
                    Log::warning("Skipping cost for OrderItem ID: {$item->id} in export. Variant or modal_price NULL.");
                }
                $totalModal += $itemCost;

                $variantName = $item->variant->name ?? 'N/A';
                $productName = $item->product->name ?? 'Produk Dihapus';
                $itemPrice = $item->price;
                $itemDetails[] = "{$productName} - {$variantName}: {$item->quantity} @ Rp" . number_format($itemPrice, 0, ',', '.');
            }
            $netProfit = $order->total_price - $totalModal;
        }

        return [
            $payment->id,
            optional($order)->id ?? 'N/A',
            optional($order)->order_date->format('d/m/Y H:i') ?? 'N/A',
            optional(optional($order)->shop)->shop_name ?? 'N/A',
            optional(optional(optional($order)->shop)->subdomain)->subdomain_name ?? 'N/A',
            optional($order->user)->name ?? 'N/A',
            optional($order->user)->email ?? 'N/A',
            ucfirst($payment->midtrans_transaction_status ?? 'N/A'),
            ucfirst($payment->midtrans_payment_type ?? 'N/A'),
            (float) optional($order)->total_price ?? 0,
            (float) $totalModal,
            (float) $netProfit,
            ucfirst(str_replace('_', ' ', optional($order)->delivery_method ?? 'Tidak Diketahui')),
            optional(optional($order)->shipping)->delivery_service ?? 'N/A',
            optional(optional($order)->shipping)->receipt_number ?? 'N/A',
            $payment->midtrans_order_id,
            implode('; ', $itemDetails),
        ];
    }
}