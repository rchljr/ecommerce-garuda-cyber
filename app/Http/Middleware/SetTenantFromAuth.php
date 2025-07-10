<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

DB::connection('central')->table('users')->where('id', $id)->first();

class SetTenantFromAuth
{
    
    public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    if ($user && $user->tenant) {
        $tenant = $user->tenant;

        config(['database.connections.tenant.database' => $tenant->db_name]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $tenant->makeCurrent(); // (jika pakai Spatie Multitenancy)
    }

    return $next($request);
}

}
