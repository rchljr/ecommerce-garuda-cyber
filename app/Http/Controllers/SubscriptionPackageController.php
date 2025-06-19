<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionPackageService;
use Illuminate\Http\Request;

class SubscriptionPackageController extends Controller
{
    protected SubscriptionPackageService $service;

    public function __construct(SubscriptionPackageService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $packages = $this->service->getAllPackages();
        // Pastikan path view ini benar
        return view('dashboard-admin.kelola-paket', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'required|string',
            'monthly_price' => 'nullable|numeric|min:0', // Diubah dari required
            'discount_year' => 'nullable|numeric|min:0|max:100',
            'is_trial' => 'required|boolean',
            'trial_days' => 'nullable|integer|min:0|required_if:is_trial,1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);

        $this->service->createPackage($validated);
        
        return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    public function showJson($id)
    {
        $package = $this->service->getPackageById($id);
        return response()->json($package);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|max:100',
            'description' => 'required|string',
            'monthly_price' => 'nullable|numeric|min:0', // Diubah dari required
            'discount_year' => 'nullable|numeric|min:0|max:100',
            'is_trial' => 'required|boolean',
            'trial_days' => 'nullable|integer|min:0|required_if:is_trial,1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);
        
        $this->service->updatePackage($id, $validated);

        return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->service->deletePackage($id);
        return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil dihapus.');
    }
}
