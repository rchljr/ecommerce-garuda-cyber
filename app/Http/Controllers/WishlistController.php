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
        // PERBAIKAN: Menggunakan guard 'customers'
        $wishlistItems = Wishlist::where('user_id', Auth::guard('customers')->id())->with('product')->get();
        return view($templatePath . '.wishlist', compact('tenant', 'wishlistItems'));
    }

    // PERBAIKAN: Menambahkan parameter $subdomain
    public function toggle(Request $request, $subdomain)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        // PERBAIKAN: Menggunakan guard 'customers'
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
