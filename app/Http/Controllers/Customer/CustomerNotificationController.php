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

        // 1. Ambil data pembayaran yang berhasil (Lunas)
        $successfulPayments = Payment::where('user_id', $user->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->with('order.subdomain')
            ->latest('updated_at')
            ->get();

        // 2. Ambil data pesanan yang statusnya gagal atau dibatalkan
        $failedOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['failed', 'cancelled', 'expired'])
            ->with('subdomain')
            ->latest('updated_at')
            ->get();

        // 3. Buat notifikasi untuk pembaruan profil
        $profileUpdateActivity = collect();
        // Cek apakah user pernah update profil (selain saat registrasi)
        if ($user->updated_at > $user->created_at->addSeconds(10)) {
            $profileUpdateActivity->push((object) [
                'type' => 'Profil',
                'title' => 'Profil Diperbarui',
                'message' => 'Data profil Anda telah berhasil diperbarui.',
                'date' => $user->updated_at,
                'status' => 'profile', // Status kustom untuk styling
                'link' => route('tenant.account.profile', ['subdomain' => request()->route('subdomain') ?? 'default']),
            ]);
        }

        // 4. Ubah setiap data menjadi format "notifikasi" yang seragam
        $paymentActivities = $successfulPayments->map(function ($payment) {
            return (object) [
                'type' => 'Pembayaran',
                'title' => 'Pembayaran Berhasil',
                'message' => 'Pembayaran untuk pesanan #' . $payment->order_id . ' telah kami terima.',
                'date' => $payment->updated_at,
                'status' => 'success',
                'link' => optional($payment->order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $payment->order->subdomain->subdomain_name]) : '#',
            ];
        });

        $orderActivities = $failedOrders->map(function ($order) {
            return (object) [
                'type' => 'Pesanan',
                'title' => 'Pesanan Dibatalkan/Gagal',
                'message' => 'Pesanan Anda dengan ID #' . $order->id . ' telah dibatalkan atau gagal.',
                'date' => $order->updated_at,
                'status' => 'cancelled',
                'link' => optional($order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $order->subdomain->subdomain_name]) : '#',
            ];
        });

        // 5. Gabungkan semua aktivitas menjadi satu koleksi
        $allActivities = $paymentActivities
            ->merge($orderActivities)
            ->merge($profileUpdateActivity);

        // 6. Urutkan semua aktivitas berdasarkan tanggal, dari yang terbaru
        $sortedActivities = $allActivities->sortByDesc('date');

        // 7. Buat paginasi secara manual
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedActivities->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedActivities = new LengthAwarePaginator($currentItems, count($sortedActivities), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('customer.notifications', ['notifications' => $paginatedActivities]);
    }
}
