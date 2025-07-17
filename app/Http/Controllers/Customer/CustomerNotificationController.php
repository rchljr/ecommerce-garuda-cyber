<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection; // Impor kelas Collection

class CustomerNotificationController extends Controller
{
    /**
     * Menampilkan feed aktivitas/notifikasi kustom untuk pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $currentSubdomain = request()->route('subdomain');

        // 1. Ambil data pembayaran yang berhasil (Lunas)
        $successfulPayments = Payment::where('user_id', $user->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->with('order.subdomain')
            ->latest('updated_at')
            ->get();

        // 2. Ambil data pesanan yang statusnya gagal, dibatalkan, atau kadaluarsa
        $failedOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['failed', 'cancelled', 'expired'])
            ->with('subdomain')
            ->latest('updated_at')
            ->get();

        // 3. Ambil data pesanan yang statusnya pending (menunggu pembayaran)
        $pendingOrders = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('subdomain')
            ->latest('updated_at')
            ->get();

        // 4. Buat notifikasi untuk pembaruan profil
        $profileUpdateActivity = new Collection(); // Gunakan new Collection()
        // Cek apakah user pernah update profil (selain saat registrasi)
        if ($user->updated_at > $user->created_at->addSeconds(10)) {
            $profileUpdateActivity->push((object) [
                'type' => 'Profil',
                'title' => 'Profil Diperbarui',
                'message' => 'Data profil Anda telah berhasil diperbarui.',
                'date' => $user->updated_at,
                'status' => 'profile', // Status kustom untuk styling
                'link' => route('tenant.account.profile', ['subdomain' => $currentSubdomain ?? 'default']),
            ]);
        }

        // 5. Ubah setiap data menjadi format "notifikasi" yang seragam
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
            $statusText = [
                'failed' => 'Pesanan Gagal',
                'cancelled' => 'Pesanan Dibatalkan',
                'expired' => 'Pesanan Kadaluarsa'
            ];
            return (object) [
                'type' => 'Pesanan',
                'title' => $statusText[$order->status] ?? 'Status Pesanan',
                'message' => 'Pesanan Anda dengan ID #' . $order->id . ' telah ' . strtolower(str_replace('Pesanan ', '', $statusText[$order->status] ?? 'diproses')) . '.',
                'date' => $order->updated_at,
                'status' => 'cancelled', // Menggunakan status 'cancelled' untuk styling (merah)
                'link' => optional($order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $order->subdomain->subdomain_name]) : '#',
            ];
        });

        $pendingOrderActivities = $pendingOrders->map(function ($order) {
            return (object) [
                'type' => 'Pesanan',
                'title' => 'Menunggu Pembayaran',
                'message' => 'Pesanan Anda dengan ID #' . $order->id . ' menunggu pembayaran.',
                'date' => $order->created_at, // Gunakan created_at untuk pesanan baru
                'status' => 'pending', // Status kustom baru
                'link' => optional($order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $order->subdomain->subdomain_name]) : '#',
            ];
        });

        // 6. Gabungkan semua aktivitas menjadi satu koleksi dasar
        $allActivities = $paymentActivities->toBase()
            ->merge($orderActivities)
            ->merge($pendingOrderActivities)
            ->merge($profileUpdateActivity);

        // 7. Urutkan semua aktivitas berdasarkan tanggal, dari yang terbaru
        $sortedActivities = $allActivities->sortByDesc('date');

        // 8. Buat paginasi secara manual
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedActivities->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedActivities = new LengthAwarePaginator($currentItems, count($sortedActivities), $perPage, $currentPage, [
            'path' => route('tenant.account.notifications', ['subdomain' => $currentSubdomain]),
        ]);

        return view('customer.notifications', ['notifications' => $paginatedActivities]);
    }
}
