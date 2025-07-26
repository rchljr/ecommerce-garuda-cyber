<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->get('tenant');
        $templatePath = $tenant->template->path;
        $wishlistItems = Wishlist::where('user_id', Auth::guard('customers')->id())
            ->with('product.varians') // Muat produk dan variannya
            ->get();
        return view($templatePath . '.wishlist', compact('tenant', 'wishlistItems'));
    }

    // app/Http/Controllers/WishlistController.php

    public function toggle(Request $request, $subdomain)
    {
        // 1. Cek apakah pengguna sudah login atau belum
        if (!Auth::guard('customers')->check()) {
            // Jika belum, kirim respon JSON dengan pesan error dan status 401 (Unauthorized)
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk menambahkan produk ke wishlist.'
            ], 401);
        }

        // Kode di bawah ini hanya akan berjalan jika pengguna sudah login
        $request->validate(['product_id' => 'required|exists:products,id']);

        $userId = Auth::guard('customers')->id();
        $productId = $request->product_id;

        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $action = 'removed';
        } else {
            Wishlist::create(['user_id' => $userId, 'product_id' => $productId]);
            $action = 'added';
        }

        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'wishlist_count' => $wishlistCount
        ]);
    }
}
