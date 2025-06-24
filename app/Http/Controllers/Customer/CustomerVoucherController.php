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
     * Menampilkan halaman voucher saya, spesifik untuk toko yang dikunjungi.
     */
    public function index(Request $request)
    {
        // 3. Dapatkan ID mitra pemilik toko berdasarkan subdomain yang diakses
        // Di aplikasi nyata, logika ini bisa berada di middleware.
        $subdomainName = explode('.', $request->getHost())[0];
        $subdomain = Subdomain::where('subdomain_name', $subdomainName)->first();

        // Jika subdomain tidak ditemukan, jangan tampilkan voucher apa pun.
        if (!$subdomain) {
            $vouchers = new LengthAwarePaginator([], 0, 8);
            return view('customer.vouchers', compact('vouchers'));
        }

        $mitraId = $subdomain->user_id;

        // 4. Ambil hanya voucher yang aktif DAN milik toko (mitra) saat ini.
        $vouchers = Voucher::where('user_id', $mitraId)
            ->where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->paginate(8);

        return view('customer.vouchers', compact('vouchers'));
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