<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Subdomain;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;


class TenantController extends Controller
{
    public function create()
    {
        $templates = Template::all();
        return view('landing-page.auth.partials.tenant', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subdomain_id' => 'required|exists:subdomains,id',
            'template_id'  => 'required|exists:templates,id',
        ]);

        $tenant = Tenant::create([
            'user_id'       => Auth::id(),
            'subdomain_id'  => $request->subdomain_id,
            'template_id'   => $request->template_id,
        ]);

        return redirect()->route('tenant.dashboard')->with('success', 'Toko berhasil dibuat!');
    }
}
