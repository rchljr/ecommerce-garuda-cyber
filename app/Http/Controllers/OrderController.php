<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipping; // PENTING: Import model Shipping
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Untuk transaksi database

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan untuk toko user.
     */
    public function index()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini karena tidak memiliki toko.');
        }

        $shopId = $shop->id;

        $orders = Order::where('shop_id', $shopId)
            ->with(['user', 'items.product', 'items.variant'])
            ->latest()
            ->paginate(15);

        return view('dashboard-mitra.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan tunggal.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop || $order->shop_id !== $shop->id) {
            abort(403, 'Anda tidak diizinkan melihat pesanan ini.');
        }

        // Memuat relasi yang detail untuk halaman detail pesanan
        $order->load([
            'user',
            'items.product',
            'items.variant',
            'voucher',
            'payment',
            'shipping', // PENTING: Eager load relasi shipping di sini
            'refundRequest',
        ]);

        return view('dashboard-mitra.orders.show', compact('order'));
    }

    /**
     * Memperbarui status pesanan.
     * Logika ini juga akan menangani input nomor resi dan metode pengiriman,
     * dan menyimpannya ke model Shipping jika relevan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop || $order->shop_id !== $shop->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['ready_for_pickup', 'shipped', 'completed'])],
            'delivery_service' => 'required_if:status,shipped|nullable|string|max:255',
            'receipt_number' => 'required_if:status,shipped|nullable|string|max:255',
        ]);


        DB::beginTransaction();

        try {
            DB::transaction(function () use ($order, $validated) {
                // Update status pesanan utama
                $order->update(['status' => $validated['status']]);

                // Logika khusus jika status diubah menjadi 'shipped'
                if ($validated['status'] === 'shipped') {
                    $order->shipping()->updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'receipt_number' => $validated['receipt_number'],
                            'status' => 'shipped', // Update juga status di tabel shipping
                        ]
                    );
                }

                // Logika khusus jika status diubah menjadi 'ready_for_pickup'
                if ($validated['status'] === 'ready_for_pickup') {
                    $order->shipping()->updateOrCreate(
                        ['order_id' => $order->id],
                        ['status' => 'ready_for_pickup']
                    );
                }
            });

            // Logika untuk detail pengiriman di model Shipping
            // if ($order->delivery_method === 'delivery' && $order->status === Order::STATUS_PROCESSING) {
            //     // Jika diantar dan statusnya 'Dikirim', nomor resi dan layanan wajib
            //     if (empty($validated['receipt_number'])) {
            //         throw new \Exception('Nomor resi wajib diisi jika metode pengiriman "Diantar" dan status diubah menjadi "Dikirim".');
            //     }
            //     if (empty($validated['delivery_service'])) {
            //         throw new \Exception('Layanan pengiriman wajib diisi jika metode pengiriman "Diantar" dan status diubah menjadi "Dikirim".');
            //     }

            //     $shipping = $order->shipping()->firstOrCreate(
            //         ['order_id' => $order->id], // Cari berdasarkan order_id
            //         [ // Data untuk create jika belum ada
            //             'delivery_service' => $validated['delivery_service'],
            //             'status' => 'on_transit',
            //             'receipt_number' => $validated['receipt_number'],
            //             'shipping_cost' => $order->shipping_cost,
            //             'estimated_delivery' => null,
            //         ]
            //     );
            //     $shipping->update([
            //         'delivery_service' => $validated['delivery_service'],
            //         'status' => 'on_transit',
            //         'receipt_number' => $validated['receipt_number'],
            //     ]);

            // } elseif ($order->delivery_method === 'pickup' && $order->status === Order::STATUS_READY_FOR_PICKUP) {
            //     $shipping = $order->shipping()->firstOrCreate(
            //         ['order_id' => $order->id], // Cari berdasarkan order_id
            //         [
            //             'delivery_service' => 'Pickup',
            //             'status' => 'ready_for_pickup',
            //             'receipt_number' => null,
            //             'shipping_cost' => 0,
            //             'estimated_delivery' => null,
            //         ]
            //     );
            //     $shipping->update([
            //         'status' => 'ready_for_pickup',
            //     ]);
            // } else {
            //     // Jika status atau metode tidak lagi membutuhkan detail shipping, hapus jika ada
            //     if ($order->shipping) {
            //         $order->shipping->delete();
            //     }
            // }

            // DB::commit();

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Status pesanan berhasil diperbarui!', // Pesan lebih umum
            //     'new_status' => $order->status,
            //     'new_delivery_method' => $order->delivery_method, // Mengambil dari DB, bukan dari request
            //     'new_tracking_number' => $order->shipping->receipt_number ?? null,
            //     'new_delivery_service' => $order->shipping->delivery_service ?? null,
            // ]);

            return back()->with('success', 'Status pesanan berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error("Failed to update order status or shipping for ID: {$order->id}. Error: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status pesanan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * [METHOD BARU] Menerima permintaan pengembalian dana.
     */
    public function approveRefund(Order $order)
    {
        $user = Auth::user();
        if ($order->shop_id !== $user->shop->id || $order->status !== 'refund_requested' || !$order->refundRequest) {
            return back()->with('error', 'Aksi tidak valid atau tidak diizinkan.');
        }

        try {
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'refunded']);
                $order->refundRequest->update(['status' => 'accepted']);

                // TODO (Opsional): Tambahkan logika untuk mengembalikan stok produk.
                // foreach($order->items as $item) {
                //     $item->variant->increment('stock', $item->quantity);
                // }
            });

            return back()->with('success', 'Permintaan refund berhasil diterima. Status pesanan diubah menjadi "Dana Dikembalikan".');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses permintaan refund: ' . $e->getMessage());
        }
    }

    /**
     * [METHOD BARU] Menolak permintaan pengembalian dana.
     */
    public function rejectRefund(Order $order)
    {
        $user = Auth::user();
        if ($order->shop_id !== $user->shop->id || $order->status !== 'refund_requested' || !$order->refundRequest) {
            return back()->with('error', 'Aksi tidak valid atau tidak diizinkan.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Kembalikan status pesanan ke 'processing' agar bisa diproses ulang oleh mitra
                $order->update(['status' => 'processing']);
                $order->refundRequest->update(['status' => 'rejected']);
            });

            return back()->with('success', 'Permintaan refund berhasil ditolak. Status pesanan dikembalikan menjadi "Diproses".');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses permintaan refund: ' . $e->getMessage());
        }
    }
}