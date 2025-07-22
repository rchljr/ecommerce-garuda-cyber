<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerOrderController extends Controller
{
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
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::guard('customers')->id()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Hanya bisa batal jika status 'pending'
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Pesanan ini tidak dapat dibatalkan.'], 422);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Pesanan berhasil dibatalkan.']);
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