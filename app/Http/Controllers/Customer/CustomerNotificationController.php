<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str; // Import Str facade

class CustomerNotificationController extends Controller
{
    /**
     * Menampilkan feed aktivitas/notifikasi kustom untuk pengguna.
     */
    public function index(Request $request, $subdomain)
    {
        /** @var \App\Models\Customer $user */
        $user = Auth::guard('customers')->user();

        if (!$user) {
            return redirect()->route('tenant.customer.login.form', ['subdomain' => $subdomain]);
        }

        $currentSubdomain = $subdomain;

        // 1. Ambil data pembayaran yang berhasil (Lunas)
        $successfulPayments = Payment::where('user_id', $user->id)
            ->whereIn('midtrans_transaction_status', ['settlement', 'capture'])
            ->with('order.subdomain') // Eager load untuk pembayaran langganan
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
        $profileUpdateActivity = new Collection();
        if ($user->updated_at > $user->created_at->addSeconds(10)) {
            $profileUpdateActivity->push((object) [
                'type' => 'Profil',
                'title' => 'Profil Diperbarui',
                'message' => 'Data profil Anda telah berhasil diperbarui.',
                'date' => $user->updated_at,
                'status' => 'profile',
                'link' => route('tenant.account.profile', ['subdomain' => $currentSubdomain]),
            ]);
        }

        // 5. Ubah setiap data menjadi format "notifikasi" yang seragam
        $paymentActivities = $successfulPayments->map(function ($payment) use ($currentSubdomain) {

            $order = $payment->order; // Ini hanya akan ada untuk pembayaran langganan

            if ($order) {
                // KASUS 1: Ini adalah pembayaran untuk satu order (misal: langganan)
                $message = 'Pembayaran untuk pesanan #' . $order->id . ' telah kami terima.';
                $subdomainName = optional($order->subdomain)->subdomain_name;
                $link = $subdomainName ? route('tenant.account.orders', ['subdomain' => $subdomainName]) : '#';
            } else {
                // KASUS 2: Ini adalah pembayaran untuk grup order (pembelian produk)
                $message = 'Pembayaran untuk pesanan #' . Str::limit($payment->order_group_id, 8) . '... telah kami terima.';
                // Arahkan ke halaman "Pesanan Saya" di subdomain saat ini
                $link = route('tenant.account.orders', ['subdomain' => $currentSubdomain]);
            }

            return (object) [
                'type' => 'Pembayaran',
                'title' => 'Pembayaran Berhasil',
                'message' => $message,
                'date' => $payment->updated_at,
                'status' => 'success',
                'link' => $link,
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
                'status' => 'cancelled',
                'link' => optional($order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $order->subdomain->subdomain_name]) : '#',
            ];
        });

        $pendingOrderActivities = $pendingOrders->map(function ($order) {
            return (object) [
                'type' => 'Pesanan',
                'title' => 'Menunggu Pembayaran',
                'message' => 'Pesanan Anda dengan ID #' . $order->id . ' menunggu pembayaran.',
                'date' => $order->created_at,
                'status' => 'pending',
                'link' => optional($order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $order->subdomain->subdomain_name]) : '#',
            ];
        });

        // 6. Gabungkan semua aktivitas
        $allActivities = $paymentActivities->toBase()
            ->merge($orderActivities)
            ->merge($pendingOrderActivities)
            ->merge($profileUpdateActivity);

        // 7. Urutkan semua aktivitas berdasarkan tanggal
        $sortedActivities = $allActivities->sortByDesc('date');

        // 8. Buat paginasi secara manual
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedActivities->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedActivities = new LengthAwarePaginator($currentItems, count($sortedActivities), $perPage, $currentPage, [
            'path' => route('tenant.account.notifications', ['subdomain' => $currentSubdomain]),
        ]);

        return view('customer.notifications', [
            'notifications' => $paginatedActivities,
            'subdomain' => $currentSubdomain
        ]);
    }
}
