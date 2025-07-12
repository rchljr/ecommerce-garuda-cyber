<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Models\Voucher;
use App\Models\Subdomain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CustomerVoucherController extends Controller
{
    /**
     * Menampilkan halaman voucher yang tersedia dari SEMUA toko,
     * dipisahkan berdasarkan toko saat ini dan toko lainnya.
     */
    public function index(Request $request)
    {
        // Ambil tenant yang sedang aktif dari middleware
        $tenant = $request->get('tenant');

        // Ambil semua voucher yang masih berlaku dan memiliki subdomain (subdomain_id)
        $allVouchers = Voucher::where('expired_date', '>=', now())
            ->whereNotNull('subdomain_id') // Hanya ambil voucher milik mitra, bukan admin
            ->with(['user.shop', 'subdomain']) // Eager load untuk efisiensi
            ->latest('created_at')
            ->get();

        // Pisahkan voucher menjadi dua koleksi: untuk toko saat ini dan toko lain
        list($currentStoreVouchers, $otherStoreVouchers) = $allVouchers->partition(function ($voucher) use ($tenant) {
            // Jika tidak ada tenant (misal, dalam mode preview), anggap semua voucher adalah "toko lain"
            if (!$tenant) {
                return false;
            }
            // Jika user_id voucher sama dengan user_id pemilik tenant, masukkan ke grup "toko saat ini"
            return $voucher->user_id === $tenant->user_id;
        });

        return view('customer.vouchers', compact('currentStoreVouchers', 'otherStoreVouchers'));
    }

    /**
     * Menangani klaim voucher oleh pengguna, memastikan voucher valid untuk toko ini.
     */
    public function claimVoucher(Request $request)
    {
        // Dapatkan ID mitra pemilik toko saat ini dari subdomain
        $subdomainName = explode('.', $request->getHost())[0];
        $subdomain = Subdomain::where('subdomain_name', $subdomainName)->first();

        if (!$subdomain) {
            return back()->withErrors(['voucher_code' => 'Toko tidak valid.']);
        }
        $mitraId = $subdomain->user_id;

        // Validasi bahwa voucher ada DAN milik toko saat ini
        $request->validate([
            'voucher_code' => [
                'required',
                'string',
                Rule::exists('vouchers')->where(function ($query) use ($mitraId) {
                    $query->where('user_id', $mitraId);
                }),
            ],
        ], [
            'voucher_code.exists' => 'Kode voucher tidak valid untuk toko ini.'
        ]);

        $voucher = Voucher::where('voucher_code', $request->voucher_code)->first();

        // Di sini Anda bisa menambahkan logika untuk mengasosiasikan voucher
        // dengan pengguna yang sedang login (misalnya, menyimpan ke tabel pivot).
        // Untuk saat ini, kita hanya akan memberikan pesan sukses.

        return back()->with('success', 'Voucher "' . strtoupper($voucher->voucher_code) . '" berhasil diklaim!');
    }
}