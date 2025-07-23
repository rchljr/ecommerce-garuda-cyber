<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
            ->with('order.subdomain')
            ->latest('updated_at')
            ->get();

        // [MODIFIKASI] 2. Ambil SEMUA pesanan pengguna
        $allOrders = Order::where('user_id', $user->id)
            ->with('subdomain')
            ->latest('updated_at')
            ->get();

        // 3. Buat notifikasi untuk pembaruan profil
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

        // 4. Ubah setiap data menjadi format "notifikasi" yang seragam
        $paymentActivities = $successfulPayments->map(function ($payment) use ($currentSubdomain) {
            $message = 'Pembayaran untuk pesanan #' . Str::limit($payment->order_group_id, 8) . '... telah kami terima.';
            $link = route('tenant.account.orders', ['subdomain' => $currentSubdomain]);

            return (object) [
                'type' => 'Pembayaran',
                'title' => 'Pembayaran Berhasil',
                'message' => $message,
                'date' => $payment->updated_at,
                'status' => 'success',
                'link' => $link,
            ];
        });

        // [MODIFIKASI] 5. Ubah SEMUA pesanan menjadi notifikasi
        $orderActivities = $allOrders->map(function ($order) {
            // Definisikan teks dan gaya untuk setiap status
            $statusDetails = [
                'pending' => ['title' => 'Menunggu Pembayaran', 'message' => 'Pesanan Anda dengan ID #%s menunggu pembayaran.', 'style' => 'pending'],
                'processing' => ['title' => 'Pesanan Diproses', 'message' => 'Pesanan Anda #%s sedang disiapkan oleh penjual.', 'style' => 'processing'],
                'shipped' => ['title' => 'Pesanan Dikirim', 'message' => 'Pesanan Anda #%s telah dikirim.', 'style' => 'shipped'],
                'ready_for_pickup' => ['title' => 'Siap Diambil', 'message' => 'Pesanan Anda #%s sudah siap untuk diambil.', 'style' => 'shipped'],
                'completed' => ['title' => 'Pesanan Selesai', 'message' => 'Pesanan Anda #%s telah selesai. Jangan lupa beri ulasan!', 'style' => 'success'],
                'cancelled' => ['title' => 'Pesanan Dibatalkan', 'message' => 'Pesanan Anda #%s telah dibatalkan.', 'style' => 'cancelled'],
                'failed' => ['title' => 'Pesanan Gagal', 'message' => 'Pembayaran untuk pesanan #%s gagal.', 'style' => 'cancelled'],
                'refund_requested' => ['title' => 'Pengajuan Refund', 'message' => 'Anda telah mengajukan pengembalian dana untuk pesanan #%s.', 'style' => 'pending'],
                'refunded' => ['title' => 'Dana Dikembalikan', 'message' => 'Pengembalian dana untuk pesanan #%s telah disetujui.', 'style' => 'refunded'],
            ];

            $detail = $statusDetails[$order->status] ?? null;

            if (!$detail) {
                return null; // Abaikan status yang tidak ingin ditampilkan sebagai notifikasi
            }

            return (object) [
                'type' => 'Pesanan',
                'title' => $detail['title'],
                'message' => sprintf($detail['message'], $order->id),
                'date' => $order->updated_at,
                'status' => $detail['style'],
                'link' => optional($order->subdomain)->subdomain_name ? route('tenant.account.orders', ['subdomain' => $order->subdomain->subdomain_name]) : '#',
            ];
        })->filter(); // Hapus item null dari koleksi

        // 6. Gabungkan semua aktivitas
        $allActivities = $paymentActivities->toBase()
            ->merge($orderActivities)
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
