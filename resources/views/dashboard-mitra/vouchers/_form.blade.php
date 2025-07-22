@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nama Voucher -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Voucher</label>
        <input type="text" name="name" id="name" value="{{ old('name', $voucher->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cth: Diskon Akhir Tahun">
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Kode Voucher -->
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700">Kode Voucher</label>
        <input type="text" name="code" id="code" value="{{ old('code', $voucher->code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cth: AKHIRTAHUN25">
        @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Nilai Diskon -->
    <div>
        <label for="discount" class="block text-sm font-medium text-gray-700">Nilai Diskon</label>
        <input type="number" name="value" id="value" value="{{ old('value', $voucher->value ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="10 (untuk 10%) atau 10000 (untuk Rp 10.000)">
        @error('discount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Minimum Pembelian -->
    <div>
        <label for="min_purchase" class="block text-sm font-medium text-gray-700">Minimum Pembelian (Rp)</label>
        <input type="number" name="min_purchase" id="min_purchase" value="{{ old('min_purchase', $voucher->min_purchase ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('min_purchase') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Berlaku Sampai -->
    <div>
        <label for="valid_until" class="block text-sm font-medium text-gray-700">Berlaku Sampai</label>
        <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', isset($voucher) ? \Carbon\Carbon::parse($voucher->valid_until)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @error('valid_until') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>

<!-- Opsi Tambahan -->
<div class="mt-6">
    <label class="flex items-center">
        <input type="checkbox" name="is_for_new_customer" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" @checked(old('is_for_new_customer', $voucher->is_for_new_customer ?? false))>
        <span class="ml-2 text-sm text-gray-600">Jadikan Voucher Selamat Datang untuk Customer Baru</span>
    </label>
</div>

<!-- Pilih Produk (Opsional) -->
<div class="mt-6">
    <label for="products" class="block text-sm font-medium text-gray-700">Berlaku untuk Produk Tertentu (Opsional)</label>
    <p class="text-xs text-gray-500 mb-2">Pilih satu atau lebih produk. Jika tidak ada yang dipilih, voucher berlaku untuk semua produk.</p>
    <select name="products[]" id="products" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" style="height: 150px;">
        @foreach($products as $product)
            <option value="{{ $product->id }}" @selected(in_array($product->id, old('products', $voucher->products->pluck('id')->toArray() ?? [])))>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>


