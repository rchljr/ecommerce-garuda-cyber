<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Services\VoucherService;

class VoucherController extends Controller
{
    protected $service;

    public function __construct(VoucherService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $vouchers = $this->service->getPaginatedVouchers($request);
        
        $search = $request->input('search');

        return view('dashboard-admin.kelola-voucher', compact('vouchers', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string|max:100|unique:vouchers,voucher_code',
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0',
            'min_spending' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'expired_date' => 'required|date|after_or_equal:start_date',
        ], [
            // Tambahkan pesan custom agar lebih ramah
            'voucher_code.unique' => 'Kode voucher ini sudah digunakan. Harap gunakan kode lain.',
        ]);

        $validated['min_spending'] = $validated['min_spending'] ?? 0;

        $this->service->createVoucher($validated);
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Voucher berhasil ditambahkan!']);
        }
        
        return redirect()->route('admin.voucher.index')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string|max:100|unique:vouchers,voucher_code,' . $id,
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'expired_date' => 'required|date|after_or_equal:start_date',
            'min_spending' => 'nullable|numeric|min:0',
        ], [
            'voucher_code.unique' => 'Kode voucher ini sudah digunakan. Harap gunakan kode lain.',
        ]);

        $this->service->updateVoucher($id, $validated);
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Voucher berhasil diperbarui!']);
        }
        
        return back()->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->service->deleteVoucher($id);
        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    public function showJson($id)
    {
        try {
            $voucher = Voucher::findOrFail($id);
            // Mengubah format tanggal agar sesuai dengan input type="date"
            $voucher->start_date = \Carbon\Carbon::parse($voucher->start_date)->format('Y-m-d');
            $voucher->expired_date = \Carbon\Carbon::parse($voucher->expired_date)->format('Y-m-d');

            return response()->json($voucher);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Voucher tidak ditemukan.'], 404);
        }
    }
}
