<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PendapatanExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * Mengambil koleksi data yang akan diekspor.
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil semua data pembayaran dengan relasi yang dibutuhkan
        return Payment::with(['user.userPackage', 'subscriptionPackage'])->latest()->get();
    }

    /**
     * Mendefinisikan header untuk setiap kolom di file Excel.
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal Transaksi',
            'Nama Mitra',
            'Email Mitra',
            'Nama Paket',
            'Jenis Tagihan',
            'Jumlah Pembayaran',
            'Status Pembayaran',
            'Metode Pembayaran',
            'ID Order Midtrans',
        ];
    }

    /**
     * Memetakan data dari setiap objek $payment ke dalam array.
     * @param mixed $payment
     * @return array
     */
    public function map($payment): array
    {
        // Logika untuk menentukan Jenis Tagihan
        $planType = optional($payment->user->userPackage)->plan_type;
        if ($planType == 'yearly') {
            $planTypeText = 'Tahunan';
        } elseif ($planType == 'monthly') {
            $planTypeText = 'Bulanan';
        } else {
            $planTypeText = 'N/A';
        }

        // Logika untuk menentukan Status Pembayaran
        $statusText = in_array($payment->midtrans_transaction_status, ['settlement', 'capture']) ? 'Lunas' : 'Belum Lunas';

        return [
            format_tanggal($payment->created_at, 'd/m/Y H:i'),
            optional($payment->user)->name,
            optional($payment->user)->email,
            optional($payment->subscriptionPackage)->package_name,
            $planTypeText,
            $payment->total_payment,
            $statusText,
            $payment->midtrans_payment_type,
            $payment->midtrans_order_id,
        ];
    }
}
