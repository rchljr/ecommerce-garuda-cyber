<div class="mb-3">
    <label for="name" class="form-label">Nama Produk</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Kategori</label>
    <select name="category_id" class="form-control" required>
        <option value="">-- Pilih Kategori --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="price" class="form-label">Harga</label>
    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="stock" class="form-label">Stok</label>
    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="product_discount" class="form-label">Diskon (%)</label>
    <input type="number" name="product_discount" class="form-control" value="{{ old('product_discount', $product->product_discount ?? '') }}">
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select name="status" class="form-control" required>
        <option value="active" {{ old('status', $product->status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ old('status', $product->status ?? '') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="mb-3">
   <label for="thumbnail" class="form-label">Thumbnail</label>
    <input type="file" name="thumbnail[]" class="form-control" id="thumbnail" multiple accept="image/*">

    @if(!empty($product->thumbnail))
        <img src="{{ asset('storage/thumbnails/'.$product->thumbnail) }}" width="100" class="mt-2">
    @endif
</div>

<button type="submit" class="btn btn-success">{{ $submit }}</button>
