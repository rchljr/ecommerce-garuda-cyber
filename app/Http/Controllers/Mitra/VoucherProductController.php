<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class VoucherProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vouchers = Auth::user()->vouchers()->latest()->paginate(10);
        return view('dashboard-mitra.vouchers.index', compact('vouchers'));
    }

    /**
     * Menampilkan form untuk membuat voucher baru.
     */
    public function create()
    {
        // Ambil produk milik mitra untuk ditampilkan di dropdown
        $products = Auth::user()->products;
        return view('dashboard-mitra.vouchers.create', compact('products'));
    }

    /**
     * Menyimpan voucher baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:vouchers,code',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'valid_until' => 'required|date|after:today',
            'is_for_new_customer' => 'nullable|boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id', // Pastikan semua ID produk valid
        ]);

        $validated['is_for_new_customer'] = $request->has('is_for_new_customer');
        $voucher = Auth::user()->vouchers()->create($validated);

        if (!empty($validated['products'])) {
            $voucher->products()->sync($validated['products']);
        }

        return redirect()->route('mitra.vouchers.index')->with('success', 'Voucher berhasil dibuat!');
    }

    /**
     * Menampilkan detail spesifik dari sebuah voucher.
     */
    public function show(Voucher $voucher)
    {
        // Otorisasi: Pastikan voucher ini milik user yang login
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $voucher->load('products'); // Muat produk yang terhubung dengan voucher ini
        return view('dashboard-mitra.vouchers.show', compact('voucher'));
    }

    /**
     * Menampilkan form untuk mengedit voucher.
     */
    public function edit(Voucher $voucher)
    {
        // Otorisasi
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $products = Auth::user()->products;
        $voucher->load('products'); // Muat produk yang sudah terpilih sebelumnya

        return view('dashboard-mitra.vouchers.edit', compact('voucher', 'products'));
    }

    /**
     * Memperbarui voucher yang ada di database.
     */
    public function update(Request $request, Voucher $voucher)
    {
        // Otorisasi
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan kode unik, tapi abaikan voucher yang sedang diedit
            'code' => ['required', 'string', Rule::unique('vouchers')->ignore($voucher->id)],
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'valid_until' => 'required|date|after:today',
            'is_for_new_customer' => 'nullable|boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        $validated['is_for_new_customer'] = $request->has('is_for_new_customer');
        $voucher->update($validated);

        // Jika tidak ada produk yang dikirim, hapus semua relasi. Jika ada, sinkronkan.
        $voucher->products()->sync($request->products ?? []);

        return redirect()->route('mitra.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    /**
     * Menghapus voucher dari database.
     */
    public function destroy(Voucher $voucher)
    {
        // Otorisasi
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $voucher->delete();

        return redirect()->route('mitra.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }
}
