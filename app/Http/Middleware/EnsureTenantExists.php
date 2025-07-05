<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class EnsureTenantExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Ambil parameter 'subdomain' dari rute
        $subdomainName = $request->route('subdomain');

        if (!$subdomainName) {
            // Seharusnya tidak pernah terjadi jika middleware hanya digunakan di grup domain
            return abort(404, 'Subdomain tidak ditemukan.');
        }

        // 2. Cari tenant di database berdasarkan nama subdomain
        $tenant = Tenant::whereHas('subdomain', function ($query) use ($subdomainName) {
            $query->where('subdomain_name', $subdomainName);
        })->first();

        // 3. Jika tenant tidak ditemukan, tampilkan halaman 404
        if (!$tenant) {
            return abort(404, 'Toko tidak ditemukan.');
        }

        // 4. Jika ditemukan, simpan objek tenant ke dalam request
        //    agar bisa diakses oleh controller.
        $request->attributes->add(['tenant' => $tenant]);

        // Bagikan variabel $currentTenant dan $currentShop
        // ke SEMUA view yang akan di-render selama request ini.
        view()->share('currentTenant', $tenant);

        if ($tenant->subdomain && $tenant->subdomain->user && $tenant->subdomain->user->shop) {
            view()->share('currentShop', $tenant->subdomain->user->shop);
        } else {
            // Sediakan objek kosong jika relasi tidak ditemukan untuk menghindari error
            view()->share('currentShop', null);
        }

        // 5. Lanjutkan request ke controller
        return $next($request);
    }
}
