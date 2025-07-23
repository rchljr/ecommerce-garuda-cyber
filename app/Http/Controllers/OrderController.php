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
            'status' => ['required', 'string', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_READY_FOR_PICKUP,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
                Order::STATUS_FAILED,
                Order::STATUS_REFUND_REQUESTED,
                Order::STATUS_REFUNDED,
            ])],
            // HAPUS validasi delivery_method dari sini
            // 'delivery_method' => ['nullable', 'string', Rule::in(['delivery', 'pickup'])],
            'delivery_service' => ['nullable', 'string', 'max:255'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $order->status = $validated['status'];
            // HAPUS baris ini: delivery_method tidak diupdate dari request
            // $order->delivery_method = $validated['delivery_method'];
            $order->save(); // Simpan perubahan status Order

            // Logika untuk detail pengiriman di model Shipping
            if ($order->delivery_method === 'delivery' && $order->status === Order::STATUS_PROCESSING) {
                // Jika diantar dan statusnya 'Dikirim', nomor resi dan layanan wajib
                if (empty($validated['receipt_number'])) {
                    throw new \Exception('Nomor resi wajib diisi jika metode pengiriman "Diantar" dan status diubah menjadi "Dikirim".');
                }
                if (empty($validated['delivery_service'])) {
                    throw new \Exception('Layanan pengiriman wajib diisi jika metode pengiriman "Diantar" dan status diubah menjadi "Dikirim".');
                }

                $shipping = $order->shipping()->firstOrCreate(
                    ['order_id' => $order->id], // Cari berdasarkan order_id
                    [ // Data untuk create jika belum ada
                        'delivery_service' => $validated['delivery_service'],
                        'status' => 'on_transit',
                        'receipt_number' => $validated['receipt_number'],
                        'shipping_cost' => $order->shipping_cost,
                        'estimated_delivery' => null,
                    ]
                );
                $shipping->update([
                    'delivery_service' => $validated['delivery_service'],
                    'status' => 'on_transit',
                    'receipt_number' => $validated['receipt_number'],
                ]);

            } elseif ($order->delivery_method === 'pickup' && $order->status === Order::STATUS_READY_FOR_PICKUP) {
                $shipping = $order->shipping()->firstOrCreate(
                    ['order_id' => $order->id], // Cari berdasarkan order_id
                    [
                        'delivery_service' => 'Pickup',
                        'status' => 'ready_for_pickup',
                        'receipt_number' => null,
                        'shipping_cost' => 0,
                        'estimated_delivery' => null,
                    ]
                );
                $shipping->update([
                    'status' => 'ready_for_pickup',
                ]);
            } else {
                // Jika status atau metode tidak lagi membutuhkan detail shipping, hapus jika ada
                if ($order->shipping) {
                    $order->shipping->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui!', // Pesan lebih umum
                'new_status' => $order->status,
                'new_delivery_method' => $order->delivery_method, // Mengambil dari DB, bukan dari request
                'new_tracking_number' => $order->shipping->receipt_number ?? null,
                'new_delivery_service' => $order->shipping->delivery_service ?? null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update order status or shipping for ID: {$order->id}. Error: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status pesanan: ' . $e->getMessage()], 500);
        }
    }
}