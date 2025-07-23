@csrf
{{-- Jika form ini juga dipakai untuk edit, pastikan ada input hidden untuk ID voucher --}}
@if (isset($voucher) && $voucher->id)
    <input type="hidden" name="_method" value="PUT"> {{-- Untuk metode PUT di update --}}
    <input type="hidden" name="id" value="{{ $voucher->id }}">
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="voucher_code" class="block text-sm font-medium text-gray-700">Nama Voucher / Kode Voucher</label>
        {{-- Menggunakan $voucher->voucher_code untuk old() dan optional() --}}
        <input type="text" name="voucher_code" id="voucher_code" value="{{ old('voucher_code', optional($voucher)->voucher_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cth: DISKONAKHIRTAHUN25">
        @error('voucher_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Voucher</label>
        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cth: Diskon 25% untuk pembelian di atas Rp 100.000">{{ old('description', optional($voucher)->description) }}</textarea>
        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="type" class="block text-sm font-medium text-gray-700">Tipe Diskon</label>
        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="percentage" {{ old('type', optional($voucher)->type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
            <option value="fixed_amount" {{ old('type', optional($voucher)->type) == 'fixed_amount' ? 'selected' : '' }}>Jumlah Tetap (Rp)</option>
        </select>
        @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="discount" class="block text-sm font-medium text-gray-700">Nilai Diskon</label>
        {{-- Menggunakan name="discount" sesuai kolom DB --}}
        <input type="number" name="discount" id="discount" value="{{ old('discount', optional($voucher)->discount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="10 (untuk 10%) atau 10000 (untuk Rp 10.000)">
        @error('discount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="min_spending" class="block text-sm font-medium text-gray-700">Minimum Pembelian (Rp)</label>
        {{-- Menggunakan name="min_spending" sesuai kolom DB --}}
        <input type="number" name="min_spending" id="min_spending" value="{{ old('min_spending', optional($voucher)->min_spending ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('min_spending') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="start_date" class="block text-sm font-medium text-gray-700">Berlaku Dari</label>
        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', optional($voucher)->start_date ? \Carbon\Carbon::parse(optional($voucher)->start_date)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="expired_date" class="block text-sm font-medium text-gray-700">Berlaku Sampai</label>
        {{-- Menggunakan name="expired_date" sesuai kolom DB --}}
        <input type="date" name="expired_date" id="expired_date" value="{{ old('expired_date', optional($voucher)->expired_date ? \Carbon\Carbon::parse(optional($voucher)->expired_date)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('expired_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mt-6">
    <label class="flex items-center">
        <input type="checkbox" name="is_for_new_customer" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" @checked(old('is_for_new_customer', optional($voucher)->is_for_new_customer))>
        <span class="ml-2 text-sm text-gray-600">Jadikan Voucher Selamat Datang untuk Customer Baru</span>
    </label>
</div>

<div class="mt-6">
    <label for="products" class="block text-sm font-medium text-gray-700">Berlaku untuk Produk Tertentu (Opsional)</label>
    <p class="text-xs text-gray-500 mb-2">Pilih satu atau lebih produk. Jika tidak ada yang dipilih, voucher berlaku untuk semua produk.</p>
    <select name="products[]" id="products" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" style="height: 150px;">
        @foreach($products as $product)
            <option value="{{ $product->id }}" @selected(in_array($product->id, old('products', optional(optional($voucher)->products)->pluck('id')->toArray() ?? [])))>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>