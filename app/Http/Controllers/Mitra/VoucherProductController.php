<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
        $products = Auth::user()->products; // Ambil produk mitra
        $voucher = new Voucher(); // Inisialisasi objek Voucher kosong
        return view('dashboard-mitra.vouchers.create', compact('products', 'voucher'));
    }

    /**
     * Menyimpan voucher baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:100', 'unique:vouchers,voucher_code'],
            'description' => 'nullable|string', // Pastikan ini ada di form dan validasi
            'min_spending' => 'nullable|numeric|min:0', // UBAH: Validasi 'min_spending' dari input 'min_purchase'
            'start_date' => 'required|date', // Pastikan ini ada di form dan validasi
            'expired_date' => 'required|date|after_or_equal:start_date', // UBAH: Validasi 'expired_date' dari input 'valid_until'
            'discount' => 'required|numeric|min:0', // UBAH: Validasi 'discount' dari input 'value'
            'is_for_new_customer' => 'nullable|boolean',
            'max_uses_per_customer' => 'nullable|boolean',
        ], [
            'voucher_code.unique' => 'Kode voucher ini sudah digunakan. Harap gunakan kode lain.',
            'expired_date.after_or_equal' => 'Tanggal berakhir voucher harus sama atau setelah tanggal mulai.',
        ]);

        $validated['is_for_new_customer'] = $request->has('is_for_new_customer'); // Checkbox
        $validated['max_uses_per_customer'] = $request->has('max_uses_per_customer'); // Checkbox

        // Mapping nama input form ke nama kolom database jika berbeda
        $validated['user_id'] = Auth::id();
        $validated['subdomain_id'] = Auth::user()->shop->subdomain->id;
        // Tidak perlu generate kode otomatis lagi karena sudah diinput di form
        // $validated['code'] = Str::upper($validated['name']); // Ini dihapus jika 'name' jadi 'voucher_code'
        
        $voucher = Voucher::create($validated);

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
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $voucher->load('products');
        return view('dashboard-mitra.vouchers.show', compact('voucher'));
    }

    /**
     * Menampilkan form untuk mengedit voucher.
     */
    public function edit(Voucher $voucher)
    {
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $products = Auth::user()->products;
        $voucher->load('products');

        return view('dashboard-mitra.vouchers.edit', compact('voucher', 'products'));
    }

    /**
     * Memperbarui voucher yang ada di database.
     */
    public function update(Request $request, Voucher $voucher)
    {
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:100', Rule::unique('vouchers', 'voucher_code')->ignore($voucher->id)],
            'description' => 'nullable|string',
            'min_spending' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'expired_date' => 'required|date|after_or_equal:start_date',
            'discount' => 'required|numeric|min:0',
            'is_for_new_customer' => 'nullable|boolean',
            'max_uses_per_customer' => 'nullable|boolean',
        ], [
            'voucher_code.unique' => 'Kode voucher ini sudah digunakan. Harap gunakan kode lain.',
            'expired_date.after_or_equal' => 'Tanggal berakhir voucher harus sama atau setelah tanggal mulai.',
        ]);

        $validated['is_for_new_customer'] = $request->has('is_for_new_customer');
        $validated['max_uses_per_customer'] = $request->has('max_uses_per_customer');
        
        // --- BARU: Gunakan nilai 'name' dari request untuk kolom 'voucher_code' ---
        // $validated['voucher_code'] = Str::upper($validated['name']); // Tidak diperlukan jika form sudah mengirim 'voucher_code'
        
        // Hapus 'name' dari $validated jika kolom 'name' tidak ada di DB
        // unset($validated['name']); // Tidak perlu unset jika sudah validasi sebagai 'voucher_code'

        $voucher->update($validated); // Update voucher dengan data yang divalidasi

        if (!empty($validated['products'])) {
            $voucher->products()->sync($validated['products']);
        } else {
            $voucher->products()->sync([]); // Jika tidak ada produk dipilih, kosongkan relasi
        }

        return redirect()->route('mitra.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    /**
     * Menghapus voucher dari database.
     */
    public function destroy(Voucher $voucher)
    {
        if ($voucher->user_id !== Auth::id()) {
            abort(403);
        }

        $voucher->delete();

        return redirect()->route('mitra.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }
}