<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Jika request tidak mengharapkan JSON (permintaan web biasa)
        if (! $request->expectsJson()) {
            
            // PERBAIKAN: Cek apakah request datang dari subdomain tenant
            if ($request->route('subdomain')) {
                // Jika ya, arahkan ke halaman login customer untuk subdomain tersebut
                return route('tenant.customer.login.form', ['subdomain' => $request->route('subdomain')]);
            }

            // Jika tidak (misalnya, untuk akses ke /admin atau /mitra), 
            // arahkan ke halaman login default. Pastikan Anda memiliki rute bernama 'login'.
            return route('login'); 
        }
    }
}