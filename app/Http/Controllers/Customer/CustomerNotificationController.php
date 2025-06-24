<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerNotificationController extends Controller
{
    /**
     * Menampilkan feed aktivitas/notifikasi kustom untuk pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil data pembayaran yang sudah berhasil (Lunas)
        $successfulPayments = Payment::where('user_id', $user->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->latest()->get();

        // 2. Ambil data pesanan yang statusnya berubah (misalnya, dibatalkan)
        $otherOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['cancelled', 'expired']) // Ganti/tambah status sesuai kebutuhan
            ->latest()->get();

        // 3. Ubah setiap data menjadi format "notifikasi" yang seragam
        $paymentActivities = $successfulPayments->map(function ($payment) {
            return (object) [
                'type' => 'Pembayaran Berhasil',
                'title' => 'Pembayaran Lunas',
                'message' => 'Pembayaran untuk pesanan ' . $payment->order_id . ' telah berhasil.',
                'date' => $payment->created_at,
                'status' => 'success', // Untuk styling
            ];
        });

        $orderActivities = $otherOrders->map(function ($order) {
            return (object) [
                'type' => 'Status Pesanan',
                'title' => 'Pesanan Dibatalkan',
                'message' => 'Pesanan Anda dengan ID ' . $order->id . ' telah dibatalkan.',
                'date' => $order->updated_at,
                'status' => 'cancelled', // Untuk styling
            ];
        });

        // 4. Gabungkan semua aktivitas menjadi satu koleksi
        $allActivities = $paymentActivities->merge($orderActivities);

        // 5. Urutkan semua aktivitas berdasarkan tanggal, dari yang terbaru
        $sortedActivities = $allActivities->sortByDesc('date');

        // 6. Buat paginasi secara manual dari koleksi yang sudah digabung
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedActivities->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedActivities = new LengthAwarePaginator($currentItems, count($sortedActivities), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('customer.notifications', ['notifications' => $paginatedActivities]);
    }
}
