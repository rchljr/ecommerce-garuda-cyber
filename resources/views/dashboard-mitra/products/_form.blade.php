<div class="mb-3">
    <label>Nama Produk</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Kategori</label>
    <input type="text" name="category_name" class="form-control" value="{{ old('category_name', $product->category->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Harga</label>
    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Stok</label>
    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Diskon (%)</label>
    <input type="number" name="product_discount" class="form-control" value="{{ old('product_discount', $product->product_discount ?? 0) }}">
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="active" {{ (old('status', $product->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ (old('status', $product->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<div class="mb-3">
    <label>Thumbnail</label>
    <input type="file" name="thumbnail" class="form-control">
    @if(isset($product->thumbnail))
        <br>
        <img src="{{ asset('storage/' . $product->thumbnail) }}" width="100">
    @endif
</div>
