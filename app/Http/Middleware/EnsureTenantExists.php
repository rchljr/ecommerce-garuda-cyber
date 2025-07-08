<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB; // PASTIKAN FACADE INI DI-IMPORT

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
            return abort(404, 'Subdomain tidak ditemukan.');
        }

        // 2. Cari tenant, ABAIKAN STATUS untuk sementara waktu demi debugging
        $tenant = Tenant::whereHas('subdomain', function ($query) use ($subdomainName) {
            $query->where('subdomain_name', $subdomainName);
                  //->where('status', 'active'); // Pengecekan status dinonaktifkan
        })
        ->first();

        // --- DEBUGGING: Tampilkan hasil pencarian tenant ---
        // Jika ini menampilkan null, berarti nama subdomain di URL salah.
        // Jika ini menampilkan data tenant, berarti masalahnya ada di status atau db_name.
        dd($tenant);
        // ---------------------------------------------------


        // 3. Jika tenant atau nama databasenya tidak ada, hentikan proses
        if (!$tenant || empty($tenant->db_name)) {
            return abort(404, 'Toko tidak ditemukan atau konfigurasi database tidak lengkap.');
        }

        // =================================================================
        // 4. LOGIKA KUNCI UNTUK SWITCH DATABASE
        // =================================================================
        config(['database.connections.mysql.database' => $tenant->db_name]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        // =================================================================

        // 5. Jika ditemukan, simpan objek tenant ke dalam request
        $request->attributes->add(['tenant' => $tenant]);

        // 6. Bagikan variabel ke SEMUA view yang akan di-render
        view()->share('currentTenant', $tenant);

        if ($tenant->subdomain && $tenant->subdomain->user && $tenant->subdomain->user->shop) {
            view()->share('currentShop', $tenant->subdomain->user->shop);
        } else {
            view()->share('currentShop', null);
        }

        // 7. Lanjutkan request ke controller
        return $next($request);
    }
}
