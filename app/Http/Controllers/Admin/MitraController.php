<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MitraController extends Controller
{
    /**
     * Menampilkan halaman daftar semua mitra dengan pengurutan prioritas.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $mitras = User::role('mitra')
            ->select('users.*')
            ->join('user_packages', 'users.id', '=', 'user_packages.user_id')
            ->with(['shop', 'subdomain', 'userPackage.subscriptionPackage'])
            // 4. Tambahkan klausa 'when' untuk menerapkan filter pencarian jika ada
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    // Cari berdasarkan nama atau email pengguna
                    $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        // Cari juga berdasarkan nama toko pada relasi 'shop'
                        ->orWhereHas('shop', function ($subQuery) use ($search) {
                        $subQuery->where('shop_name', 'like', "%{$search}%");
                    })
                        // Cari juga berdasarkan nama subdomain pada relasi 'subdomain'
                        ->orWhereHas('subdomain', function ($subQuery) use ($search) {
                        $subQuery->where('subdomain_name', 'like', "%{$search}%");
                    });
                });
            })
            // Urutkan dengan prioritas:
            ->orderByRaw("
                CASE
                    WHEN user_packages.status = 'active' THEN 1
                    ELSE 2
                END
            ")
            // urutkan berdasarkan tanggal berakhir terdekat
            ->orderBy('user_packages.expired_date', 'asc')
            ->paginate(5)->appends($request->query());

        $categoryMap = Category::pluck('name', 'slug')->all();

        return view('dashboard-admin.kelola-mitra', compact('mitras', 'categoryMap'));
    }

    /**
     * Menonaktifkan paket langganan seorang mitra.
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:inactive',
        ]);

        if ($user->hasRole('mitra') && $user->userPackage) {
            try {
                // Update status di userPackage.
                $user->userPackage->update(['status' => 'inactive']);

                // menonaktifkan subdomain
                if ($user->subdomain) {
                    $user->subdomain->update(['status' => 'inactive']);
                }

                return back()->with('success', 'Status mitra berhasil diubah menjadi Tidak Aktif.');
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat mengubah status mitra.');
            }
        }

        return back()->with('error', 'Gagal menemukan data paket untuk mitra ini.');
    }
}
