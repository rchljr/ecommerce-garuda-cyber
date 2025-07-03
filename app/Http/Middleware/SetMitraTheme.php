<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Subdomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SetMitraTheme
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
        // Ambil host dari request (misal: toko-abc.garuda.id)
        $host = $request->getHost();
        $subdomainName = explode('.', $host)[0];

        // Layout default jika tidak ada subdomain atau tema kustom
        $themeLayout = 'layouts.customer'; 
        $currentShop = null;

        $subdomain = Subdomain::where('subdomain_name', $subdomainName)->with('user.shop')->first();

        if ($subdomain && $subdomain->user && $subdomain->user->shop) {
            $currentShop = $subdomain->user->shop;
            $customTheme = $currentShop->theme;

            // Cek jika mitra punya tema kustom dan file view-nya ada
            if ($customTheme && view()->exists($customTheme)) {
                $themeLayout = $customTheme;
            }
        }

        // Bagikan variabel layout dan data toko ke semua view untuk request ini
        View::share('theme_layout', $themeLayout);
        View::share('currentShop', $currentShop);

        return $next($request);
    }
}
