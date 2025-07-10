<?php

namespace App\TenantFinder;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class PathTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $tenantSlug = $request->route('subdomain');

        if (! $tenantSlug) {
            return null;
        }

        // Cari tenant seperti biasa
        $tenant = Tenant::whereHas('subdomain', function ($query) use ($tenantSlug) {
            $query->where('subdomain_name', $tenantSlug);
        })->first();

        // --- DEBUGGING FINAL ---
        // Hentikan aplikasi dan tampilkan isi dari objek tenant yang ditemukan.
        // Perhatikan bagian '#attributes'. Apakah ada 'db_name' di dalamnya?
        dd($tenant);
        // -----------------------

        return $tenant;
    }
}
