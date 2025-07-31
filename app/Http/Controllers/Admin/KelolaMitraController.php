<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Category;
use App\Models\UserPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\MitraDeactivatedNotification;
use App\Notifications\MitraReactivatedNotification;

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
    public function deactivate(User $user)
    {
        try {
            $user->load('userPackage', 'subdomain');

            if ($user->hasRole('mitra') && $user->userPackage) {

                $user->userPackage->update(['status' => 'pending']);

                if ($user->subdomain) {
                    $user->subdomain->update(['status' => 'pending']);
                    $user->subdomain->update(['publication_status' => 'pending']);
                }

                try {
                    $user->notify(new MitraDeactivatedNotification($user));
                } catch (\Exception $e) {
                    // Jika notifikasi gagal terkirim, proses tetap lanjut tapi catat errornya
                    Log::error('Gagal mengirim notifikasi penonaktifan ke mitra: ' . $user->email, ['error' => $e->getMessage()]);
                }

                return back()->with('success', "Mitra '{$user->name}' berhasil dinonaktifkan.");
            }

            return back()->with('error', 'Gagal memproses. Data paket untuk mitra ini tidak ditemukan di database.');

        } catch (\Exception $e) {
            // Sekarang $user->id akan memiliki nilai yang benar untuk logging
            Log::error('Deactivate Mitra Exception: ' . $e->getMessage(), ['mitra_id' => $user->id]);
            return back()->with('error', 'Terjadi kesalahan pada server saat mencoba menonaktifkan mitra.');
        }
    }

    /**
     * Mengaktifkan kembali paket langganan seorang mitra.
     */
    public function reactivate(User $user)
    {
        try {
            $user->load('userPackage', 'subdomain');

            if ($user->hasRole('mitra') && $user->userPackage) {

                $userPackage = $user->userPackage;

                $newExpiredDate = Carbon::now()->addMonth();
                if ($userPackage->plan_type === 'yearly') {
                    $newExpiredDate = Carbon::now()->addYear();
                }

                $userPackage->update([
                    'status' => 'active',
                    'expired_date' => $newExpiredDate,
                    'active_date' => Carbon::now()
                ]);

                if ($user->subdomain) {
                    $user->subdomain->update(['status' => 'active']);
                }

                try {
                    $user->notify(new MitraReactivatedNotification($user));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim notifikasi re-aktivasi ke mitra: ' . $user->email, ['error' => $e->getMessage()]);
                }

                return back()->with('success', "Mitra '{$user->name}' berhasil diaktifkan kembali.");
            }

            return back()->with('error', 'Gagal memproses. Data paket untuk mitra ini tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Reactivate Mitra Exception: ' . $e->getMessage(), ['mitra_id' => $user->id]);
            return back()->with('error', 'Terjadi kesalahan pada server saat mencoba mengaktifkan kembali mitra.');
        }
    }
}
