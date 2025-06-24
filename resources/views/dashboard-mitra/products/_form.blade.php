<div class="mb-4"> {{-- Mengganti mb-3 dengan mb-4 untuk spacing lebih baik --}}
    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
    <input type="text" name="name" id="name"
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
           value="{{ old('name', $product->name ?? '') }}" required
           placeholder="Masukkan nama produk">
    @error('name')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
    {{-- PENTING: Menggunakan SELECT untuk Kategori --}}
    <select name="category_id" id="category_id"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
            required>
        <option value="">Pilih Kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
    <textarea name="description" id="description" rows="4"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
              placeholder="Deskripsi singkat produk">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
    <input type="number" name="price" id="price"
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
           value="{{ old('price', $product->price ?? '') }}" required min="0" step="0.01"
           placeholder="Contoh: 150000">
    @error('price')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
    <input type="number" name="stock" id="stock"
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
           value="{{ old('stock', $product->stock ?? '') }}" required min="0"
           placeholder="Jumlah stok tersedia">
    @error('stock')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="product_discount" class="block text-gray-700 text-sm font-bold mb-2">Diskon (%)</label>
    <input type="number" name="product_discount" id="product_discount"
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
           value="{{ old('product_discount', $product->product_discount ?? 0) }}" min="0" max="100"
           placeholder="Contoh: 10 (untuk 10%)">
    @error('product_discount')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
    <select name="status" id="status"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500">
        <option value="active" {{ (old('status', $product->status ?? '') == 'active') ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ (old('status', $product->status ?? '') == 'inactive') ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
    @error('status')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="thumbnail" class="block text-gray-700 text-sm font-bold mb-2">Thumbnail</label>
    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
           class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100 cursor-pointer">
    @error('thumbnail')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror

    @if(isset($product->thumbnail) && $product->thumbnail)
        <div class="mt-4">
            <p class="text-gray-700 text-sm mb-2">Thumbnail Saat Ini:</p>
            <img src="{{ asset('storage/thumbnails/' . $product->thumbnail) }}"
                 alt="Current Thumbnail" class="w-32 h-32 object-cover rounded-lg shadow-md border border-gray-200">
        </div>
    @endif
</div>