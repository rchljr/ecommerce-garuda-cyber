<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Category;
use App\Models\UserPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class KelolaMitraController extends Controller
{
    /**
     * Menampilkan halaman daftar semua mitra dengan pengurutan prioritas.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $mitras = User::role('mitra')
            ->with(['shop', 'subdomain', 'userPackage.subscriptionPackage'])
            ->whereHas('userPackage')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('shop', fn($sub) => $sub->where('shop_name', 'like', "%{$search}%"))
                        ->orWhereHas('subdomain', fn($sub) => $sub->where('subdomain_name', 'like', "%{$search}%"));
                });
            })
            // Mengurutkan berdasarkan status dan tanggal dari relasi
            ->orderBy(
                UserPackage::select('status')
                    ->whereColumn('user_packages.user_id', 'users.id')
                    ->orderBy('status', 'asc')
                    ->limit(1)
            )
            ->orderBy(
                UserPackage::select('expired_date')
                    ->whereColumn('user_packages.user_id', 'users.id')
                    ->orderBy('expired_date', 'asc')
                    ->limit(1)
            )
            ->paginate(5)->appends($request->query());


        $categoryMap = Category::pluck('name', 'slug')->all();

        return view('dashboard-admin.kelola-mitra', compact('mitras', 'categoryMap'));
    }
    /**
     * Menonaktifkan paket langganan seorang mitra.
     */
    public function deactivate(User $mitra)
    {
        if ($mitra->hasRole('mitra') && $mitra->userPackage) {
            try {
                // Update status di userPackage menjadi 'pending'
                $mitra->userPackage->update(['status' => 'pending']);

                // Menonaktifkan subdomain
                if ($mitra->subdomain) {
                    $mitra->subdomain->update(['status' => 'pending']);
                }

                return back()->with('success', "Mitra '{$mitra->name}' berhasil dinonaktifkan.");
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'Gagal menemukan data paket untuk mitra ini.');
    }

    /**
     * Mengaktifkan kembali paket langganan seorang mitra.
     */
    public function reactivate(Request $request, $mitraId)
    {
        $mitra = User::with('userPackage', 'subdomain')->findOrFail($mitraId);

        if ($mitra->hasRole('mitra') && $mitra->userPackage) {
            try {
                $userPackage = $mitra->userPackage;
                
                $newExpiredDate = Carbon::now()->addMonth();
                if ($userPackage->plan_type === 'yearly') {
                    $newExpiredDate = Carbon::now()->addYear();
                }

                $userPackage->update([
                    'status' => 'active',
                    'expired_date' => $newExpiredDate,
                    'active_date' => Carbon::now()
                ]);

                if ($mitra->subdomain) {
                    $mitra->subdomain->update(['status' => 'active']);
                }

                return back()->with('success', "Mitra '{$mitra->name}' berhasil diaktifkan kembali.");
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
        return back()->with('error', 'Gagal menemukan data paket untuk mitra ini.');
    }
}
