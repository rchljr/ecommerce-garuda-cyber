<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\RefundRequest;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerOrderController extends Controller
{
    protected $midtransService;

    // Inject MidtransService melalui constructor
    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }
    /**
     * Menampilkan daftar pesanan milik pengguna yang sedang login.
     */
    public function index(Request $request, $subdomain)
    {
        $search = $request->input('search');
        $user = Auth::guard('customers')->user();

        $ordersQuery = Order::where('user_id', $user->id)
            ->with([
                'subdomain.user.shop',
                'items.product', // Eager load relasi gambar utama
                'items.variant',
                'shipping',
                'voucher',
                'testimonials' => function ($query) {
                    $query->where('user_id', Auth::guard('customers')->id());
                },
                'refundRequest' // Eager load data refund
            ])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('subdomain.user.shop', function ($subQuery) use ($search) {
                        $subQuery->where('shop_name', 'like', "%{$search}%");
                    })->orWhereHas('items.product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    })->orWhere('id', 'like', "%{$search}%"); // Tambah pencarian via ID Order
                });
            })
            ->latest()
            ->paginate(10);

        return view('customer.orders', [
            'orders' => $ordersQuery,
            'search' => $search,
            'subdomain' => $subdomain,
        ]);
    }

    /**
     * Membatalkan pesanan (status: pending).
     */
    public function cancel(Request $request, $subdomain, Order $order)
    {
        $customer = Auth::guard('customers')->user();

        // 1. Validasi & Otorisasi
        if ($order->user_id !== $customer->id) {
            return response()->json(['message' => 'Aksi tidak diizinkan.'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Pesanan ini tidak dapat dibatalkan lagi.'], 422);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::where('order_group_id', $order->order_group_id)->first();
            $midtransStatusUpdated = false;

            if ($payment) {
                try {
                    // Coba batalkan transaksi di Midtrans
                    $this->midtransService->cancelTransaction($payment->midtrans_order_id);
                    $payment->update(['midtrans_transaction_status' => 'cancel']);
                    $midtransStatusUpdated = true;
                } catch (\Exception $e) {
                    // [PERBAIKAN] Tangani error 412 dari Midtrans secara khusus
                    if (str_contains($e->getMessage(), '412')) {
                        // Anggap ini sukses, karena transaksi sudah dalam status final (expire/cancel)
                        Log::warning('Midtrans cancellation failed with 412, proceeding to cancel locally.', [
                            'order_id' => $order->id,
                            'midtrans_order_id' => $payment->midtrans_order_id
                        ]);
                        // Update status lokal kita menjadi 'cancel' agar sinkron
                        $payment->update(['midtrans_transaction_status' => 'cancel']);
                        $midtransStatusUpdated = true;
                    } else {
                        // Jika error lain, lempar kembali agar transaksi di-rollback
                        throw $e;
                    }
                }
            }

            // Update status semua order dalam grup yang sama menjadi 'cancelled'
            Order::where('order_group_id', $order->order_group_id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            // Kembalikan stok produk
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Pesanan berhasil dibatalkan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membatalkan pesanan oleh pelanggan.', [
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Terjadi kesalahan saat mencoba membatalkan pesanan.'], 500);
        }
    }

    /**
     * Mengonfirmasi penerimaan pesanan.
     */
    public function receive(Request $request, $subdomain, Order $order)
    {
        if ($order->user_id !== Auth::guard('customers')->id()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Hanya bisa konfirmasi jika status 'shipped' atau 'ready_for_pickup'
        if (!in_array($order->status, ['shipped', 'ready_for_pickup'])) {
            return response()->json(['message' => 'Status pesanan tidak memungkinkan untuk aksi ini.'], 422);
        }

        $order->update(['status' => 'completed']);

        // Di sini Anda bisa menambahkan logika pemberian poin, dll.

        return response()->json(['message' => 'Terima kasih telah mengonfirmasi pesanan.']);
    }

    /**
     * Mengajukan permintaan refund.
     */
    public function requestRefund(Request $request, $subdomain, Order $order)
    {
        if ($order->user_id !== Auth::guard('customers')->id()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Validasi input
        $validated = $request->validate([
            'bank_account_number' => 'required|string|numeric|min:8',
            'reason' => 'required|string|min:10|max:500',
        ]);

        // Kondisi utama: status harus 'processing' dan belum ada refund sebelumnya.
        if ($order->status !== 'processing' || $order->refundRequest) {
            return response()->json(['message' => 'Pesanan ini tidak dapat diajukan refund.'], 422);
        }

        DB::transaction(function () use ($order, $validated) {
            // Buat entri permintaan refund
            RefundRequest::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'bank_account_number' => $validated['bank_account_number'],
                'amount' => $order->total_price,
                'status' => 'pending',
                'reason' => $validated['reason'],
            ]);

            // Ubah status order menjadi 'refund_pending' atau sejenisnya
            $order->update(['status' => 'refund_pending']);
        });

        return response()->json(['message' => 'Permintaan refund Anda telah diajukan dan akan segera diproses.']);
    }

    /**
     * Logika untuk auto-complete (dijalankan oleh scheduler).
     * Anda perlu membuat Command dan menjadwalkannya.
     * Contoh: php artisan make:command AutoCompleteOrders
     */
    public static function runAutoCompleteOrders()
    {
        $sevenDaysAgo = now()->subDays(7);

        // Cari order dengan status 'shipped' atau 'ready_for_pickup' yang
        // terakhir di-update lebih dari 7 hari yang lalu.
        Order::whereIn('status', ['shipped', 'ready_for_pickup'])
            ->where('updated_at', '<=', $sevenDaysAgo)
            ->update(['status' => 'completed']);
    }
}