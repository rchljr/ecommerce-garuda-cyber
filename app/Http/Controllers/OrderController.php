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
    public function index(Request $request) // 1. Tambahkan Request $request
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini karena tidak memiliki toko.');
        }

        $shopId = $shop->id;

        // 2. Mulai query builder, jangan langsung get() atau paginate()
        $query = Order::where('shop_id', $shopId)
            ->with(['user']); // Eager load relasi user

        // 3. Terapkan filter berdasarkan input dari form
        // Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan Metode Pengiriman
        if ($request->filled('delivery_method')) {
            // Pastikan Anda memiliki kolom 'shipping_method' di tabel 'orders'
            $query->where('delivery_method', $request->delivery_method);
        }

        // 4. Lakukan sorting dan pagination setelah semua filter diterapkan
        $orders = $query->latest()->paginate(10); // Ubah paginate ke 10

        // 5. Kirim data ke view
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
            // 1. Update status pesanan utama
            $order->status = $validated['status'];
            $order->save();

            // 2. Update atau buat data pengiriman terkait
            if ($validated['status'] === 'shipped') {
                $order->shipping()->updateOrCreate(
                    ['order_id' => $order->id], // Kondisi pencarian
                    [ // Data untuk di-update atau di-create
                        'delivery_service' => $validated['delivery_service'],
                        'receipt_number' => $validated['receipt_number'],
                        'status' => 'shipped',
                    ]
                );
            } elseif ($validated['status'] === 'ready_for_pickup') {
                $order->shipping()->updateOrCreate(
                    ['order_id' => $order->id],
                    ['status' => 'ready_for_pickup']
                );
            } elseif ($validated['status'] === 'completed') {
                // Jika pesanan selesai, update juga status di tabel shipping jika ada
                if ($order->shipping) {
                    $newShippingStatus = $order->delivery_method === 'pickup' ? 'picked_up' : 'delivered';
                    $order->shipping->status = $newShippingStatus;
                    $order->shipping->save();
                }
            }

            // 3. Jika semua berhasil, commit transaksi
            DB::commit();

            return back()->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            // 4. Jika ada error, rollback semua perubahan
            DB::rollBack();

            Log::error("Failed to update order status for ID: {$order->id}. Error: " . $e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Terjadi kesalahan internal. Gagal memperbarui status pesanan.');
        }
    }

    /**
     * [METHOD BARU] Menerima permintaan pengembalian dana.
     */
    public function approveRefund(Order $order)
    {
        $user = Auth::user();
        if ($order->shop_id !== $user->shop->id || $order->status !== 'refund_pending' || !$order->refundRequest) {
            return back()->with('error', 'Aksi tidak valid atau tidak diizinkan.');
        }

        try {
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'refunded']);
                $order->refundRequest->update(['status' => 'approved']);

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
        if ($order->shop_id !== $user->shop->id || $order->status !== 'refund_pending' || !$order->refundRequest) {
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
