<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Menampilkan halaman wishlist pengguna.
     */
    public function index(Request $request)
    {
        // 1. Ambil data tenant dan path template dari middleware
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;

        // 2. Logika Anda untuk mengambil item wishlist (tidak berubah)
        $wishlistItems = Wishlist::where('user_id', Auth::id())->with('product')->get();

        // 3. Tampilkan view dari template yang benar
        return view($templatePath . '.wishlist', compact('tenant', 'wishlistItems'));
    }

    /**
     * Menambah atau menghapus produk dari wishlist via AJAX.
     */
    public function toggle(Request $request)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $request->validate(['product_id' => 'required|exists:products,id']);

        $userId = Auth::id();
        $productId = $request->product_id;

        // Cek apakah produk sudah ada di wishlist
        $wishlistItem = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($wishlistItem) {
            // Jika sudah ada, hapus dari wishlist
            $wishlistItem->delete();
            $action = 'removed';
        } else {
            // Jika belum ada, tambahkan ke wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $action = 'added';
        }

        // Hitung jumlah wishlist terbaru
        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'action' => $action, // 'added' atau 'removed'
            'wishlist_count' => $wishlistCount
        ]);
    }
}
