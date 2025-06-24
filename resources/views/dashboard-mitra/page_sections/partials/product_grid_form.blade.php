{{-- resources/views/admin/page_sections/partials/product_grid_form.blade.php --}}
{{-- Variabel $content akan berisi data lama (old() atau dari database saat edit) --}}

<h3 class="text-xl font-semibold text-gray-700 mb-4">Konten Seksi Grid Produk</h3>

<div class="mb-4">
    <label for="content_heading" class="block text-gray-700 text-sm font-bold mb-2">Judul Seksi (Opsional):</label>
    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="content_heading" name="content[heading]" value="{{ $content['heading'] ?? '' }}" placeholder="Contoh: Produk Terlaris, Produk Terbaru">
</div>

<div class="mb-4">
    <label for="content_filter_type" class="block text-gray-700 text-sm font-bold mb-2">Tipe Produk yang Ditampilkan:</label>
    <select name="content[filter_type]" id="content_filter_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <option value="latest" {{ ($content['filter_type'] ?? 'latest') == 'latest' ? 'selected' : '' }}>Produk Terbaru</option>
        <option value="featured" {{ ($content['filter_type'] ?? '') == 'featured' ? 'selected' : '' }}>Produk Unggulan</option>
        <option value="bestsellers" {{ ($content['filter_type'] ?? '') == 'bestsellers' ? 'selected' : '' }}>Produk Terlaris</option>
        <option value="custom" {{ ($content['filter_type'] ?? '') == 'custom' ? 'selected' : '' }}>Pilih Produk Manual</option>
    </select>
</div>

{{-- Bagian ini akan muncul jika 'Pilih Produk Manual' dipilih --}}
<div id="custom-products-selection" class="mb-4 p-4 border rounded-md bg-gray-50 {{ ($content['filter_type'] ?? '') == 'custom' ? '' : 'hidden' }}">
    <h4 class="text-lg font-semibold text-gray-800 mb-3">Pilih Produk Manual (Berdasarkan ID)</h4>
    <p class="text-sm text-gray-600 mb-3">Masukkan ID produk, pisahkan dengan koma (contoh: 1,5,12).</p>
    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="content[product_ids]" value="{{ implode(',', $content['product_ids'] ?? []) }}" placeholder="Contoh: 1, 5, 12">
</div>

<div class="mb-4">
    <label for="content_display_limit" class="block text-gray-700 text-sm font-bold mb-2">Jumlah Produk yang Ditampilkan:</label>
    <input type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="content_display_limit" name="content[display_limit]" value="{{ $content['display_limit'] ?? 8 }}" min="1">
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterTypeSelect = document.getElementById('content_filter_type');
        const customProductsSelection = document.getElementById('custom-products-selection');

        function toggleCustomProductsSelection() {
            if (filterTypeSelect.value === 'custom') {
                customProductsSelection.classList.remove('hidden');
            } else {
                customProductsSelection.classList.add('hidden');
            }
        }

        filterTypeSelect.addEventListener('change', toggleCustomProductsSelection);
        toggleCustomProductsSelection(); // Panggil saat memuat halaman untuk inisialisasi awal
    });
</script>
@endpush
