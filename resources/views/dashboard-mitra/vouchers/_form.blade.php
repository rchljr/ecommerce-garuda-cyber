@csrf
{{-- Jika ini adalah form edit, tambahkan method PUT --}}
@if ($voucher->exists)
    @method('PUT')
@endif

<div class="p-6 space-y-6">
    {{-- Baris 1: Kode Voucher & Diskon --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="voucher_code" class="block text-sm font-medium text-gray-700">Kode Voucher</label>
            <input type="text" name="voucher_code" id="voucher_code"
                value="{{ old('voucher_code', $voucher->voucher_code) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="cth: DISKONGAJIAN" required>
            @error('voucher_code') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="discount" class="block text-sm font-medium text-gray-700">Nilai Diskon (%)</label>
            <input type="number" name="discount" id="discount" value="{{ old('discount', $voucher->discount) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="10" required min="1" max="100">
            @error('discount') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Voucher</label>
        <textarea name="description" id="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            placeholder="cth: Diskon 10% untuk semua produk fashion">{{ old('description', $voucher->description) }}</textarea>
        @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>

    {{-- Minimum Belanja --}}
    <div>
        <label for="min_spending" class="block text-sm font-medium text-gray-700">Minimum Pembelian (Rp)</label>
        <input type="number" name="min_spending" id="min_spending"
            value="{{ old('min_spending', $voucher->min_spending ?? 0) }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            min="0">
        @error('min_spending') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>

    {{-- Periode Aktif --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Berlaku Dari</label>
            <input type="date" name="start_date" id="start_date"
                value="{{ old('start_date', optional($voucher->start_date)->format('Y-m-d')) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>
            @error('start_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="expired_date" class="block text-sm font-medium text-gray-700">Berlaku Sampai</label>
            <input type="date" name="expired_date" id="expired_date"
                value="{{ old('expired_date', optional($voucher->expired_date)->format('Y-m-d')) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>
            @error('expired_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Opsi Checkbox --}}
    <div class="border-t pt-6 space-y-4">
        <label class="flex items-center">
            <input type="checkbox" name="is_for_new_customer" value="1"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200"
                @checked(old('is_for_new_customer', $voucher->is_for_new_customer))>
            <span class="ml-3 text-sm text-gray-600">Jadikan Voucher Selamat Datang untuk Customer Baru</span>
        </label>
        <label class="flex items-center">
            <input type="checkbox" name="max_uses_per_customer" value="1"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200"
                @checked(old('max_uses_per_customer', $voucher->max_uses_per_customer == 1))>
            <span class="ml-3 text-sm text-gray-600">Batasi Voucher Dapat Digunakan Hanya 1 Kali / Pelanggan</span>
        </label>
    </div>
</div>