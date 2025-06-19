<?php

namespace App\Http\Controllers;

use App\Models\Subdomain;
use Illuminate\Http\Request;

class SubdomainController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function checkSubdomain(Request $request)
    {
        $request->validate(['subdomain' => 'required|string']);

        $exists = Subdomain::where('subdomain_name', $request->subdomain)->exists();

        return response()->json([
            'subdomain' => $request->subdomain,
            'available' => !$exists,
            'status' => $exists ? 'Tidak Tersedia' : 'Tersedia',
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
