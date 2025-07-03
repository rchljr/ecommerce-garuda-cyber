<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek guard 'web' (untuk admin/mitra)
        if (Auth::guard('web')->check() && in_array(Auth::guard('web')->user()->role, $roles)) {
            return $next($request);
        }

        // Cek guard 'customer'
        if (Auth::guard('customer')->check() && in_array(Auth::guard('customer')->user()->role, $roles)) {
            return $next($request);
        }
        
        // Jika tidak ada yang cocok, abort
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}