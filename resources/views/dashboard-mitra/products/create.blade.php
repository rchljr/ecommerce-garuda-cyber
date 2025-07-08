{{-- Diasumsikan Anda memiliki layout untuk dashboard admin --}}
@extends('layouts.mitra')

@section('title', 'Tambah Produk Baru')

@push('styles')
    {{-- Style khusus untuk tagging dan upload gambar --}}
    <style>
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            background-color: #3b82f6;
            color: white;
            padding: 4px 8px;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .tag-close {
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .image-preview-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
        }

        .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            border: 2px solid white;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Produk Baru</h1>
        @if ($errors->any())
            <div
                style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                <strong style="font-weight: bold;">Oops! Ada beberapa masalah dengan input Anda:</strong>
                <ul style="list-style-type: disc; margin-left: 1.5rem; margin-top: 0.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div
                style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem;">
                <strong style="font-weight: bold;">Error!</strong>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        {{-- Form utama --}}
        <form action="{{ route('mitra.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Kolom Kiri (Info Utama) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Nama & Deskripsi --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Informasi Dasar</h2>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                            <input type="text" id="name" name="name"
                                class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi
                                Singkat</label>
                            <textarea id="short_description" name="short_description" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Lengkap
                                </p>
                                {{-- Disarankan menggunakan editor WYSIWYG seperti TinyMCE atau CKEditor di sini --}}
                                <textarea id="description" name="description" rows="8" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                    </div>

                    {{-- Gambar & Galeri --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Gambar Produk</h2>
                        <div class="mb-4">
                            <label for="main_image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama
                                (Thumbnail)</label>
                            <input type="file" id="main_image" name="main_image" class="w-full" accept="image/*">
                            <div id="main-image-preview-container" class="image-preview-container"></div>
                        </div>
                        <div>
                            <label for="gallery_images" class="block text-sm font-medium text-gray-700 mb-1">Galeri Gambar
                                (Bisa pilih banyak)</label>
                            <input type="file" id="gallery_images" name="gallery_images[]" class="w-full"
                                accept="image/*" multiple>
                            <div id="gallery-preview-container" class="image-preview-container"></div>
                        </div>
                    </div>

                    {{-- Varian Produk --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Varian Produk</h2>
                        <div id="variants-container">
                            {{-- Varian pertama (contoh) --}}
                            <div class="variant-item grid grid-cols-1 md:grid-cols-4 gap-4 items-end border-b pb-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Warna</label>
                                    <input type="text" name="variants[0][color]" placeholder="e.g., Merah"
                                        class="w-full border-gray-300 rounded-md shadow-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ukuran</label>
                                    <input type="text" name="variants[0][size]" placeholder="e.g., XL"
                                        class="w-full border-gray-300 rounded-md shadow-sm mt-1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stok</label>
                                    <input type="number" name="variants[0][stock]" placeholder="e.g., 50"
                                        class="w-full border-gray-300 rounded-md shadow-sm mt-1" min="0">
                                </div>
                                <button type="button"
                                    class="remove-variant-btn bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-600 text-sm">Hapus</button>
                            </div>
                        </div>
                        <button type="button" id="add-variant-btn"
                            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">
                            + Tambah Varian
                        </button>
                    </div>
                </div>

                <!-- Contoh penempatan di kolom kanan (Pengaturan) -->

                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Pengaturan Tampilan</h2>
                        <div class="space-y-4">
                            <label for="is_best_seller" class="flex items-center">
                                <input type="checkbox" id="is_best_seller" name="is_best_seller" value="1"
                                    {{ isset($product) && $product->is_best_seller ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai Best Seller</span>
                            </label>
                            <label for="is_new_arrival" class="flex items-center">
                                <input type="checkbox" id="is_new_arrival" name="is_new_arrival" value="1"
                                    {{ isset($product) && $product->is_new_arrival ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai New Arrival</span>
                            </label>
                            <label for="is_hot_sale" class="flex items-center">
                                <input type="checkbox" id="is_hot_sale" name="is_hot_sale" value="1"
                                    {{ isset($product) && $product->is_hot_sale ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai Hot Sale</span>
                            </label>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Harga & SKU</h2>
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga Utama
                                (Rp)</label>
                            <input type="number" id="price" name="price"
                                class="w-full border-gray-300 rounded-md shadow-sm" required min="0">
                        </div>
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU (Stock Keeping
                                Unit)</label>
                            <input type="text" id="sku" name="sku"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Organisasi</h2>
                        <div class="mb-4">
                            <label for="sub_category_id">Sub Kategori:</label>
                            <select name="sub_category_id" id="sub_category_id" required>
                                <option value="">Pilih Sub Kategori</option>
                                @foreach ($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}"
                                        {{ old('sub_category_id', optional($product ?? null)->sub_category_id) == $subCategory->id ? 'selected' : '' }}>
                                        {{ $subCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tags-input" class="block text-sm font-medium text-gray-700 mb-1">Tag Produk
                                (pisahkan dengan koma)</label>
                            <input type="text" id="tags-input" class="w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="e.g., Baju, Musim Panas, Katun">
                            <input type="hidden" name="tags" id="tags-hidden">
                            <div id="tags-container" class="tag-container mt-2"></div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <button type="submit"
                            class="w-full bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 font-semibold">
                            Simpan Produk
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika Varian ---
            const variantsContainer = document.getElementById('variants-container');
            const addVariantBtn = document.getElementById('add-variant-btn');
            let variantIndex = 1;

            addVariantBtn.addEventListener('click', () => {
                const variantItem = document.createElement('div');
                variantItem.className =
                    'variant-item grid grid-cols-1 md:grid-cols-4 gap-4 items-end border-b pb-4 mb-4';
                variantItem.innerHTML = `
            <div><label class="block text-sm font-medium text-gray-700">Warna</label><input type="text" name="variants[${variantIndex}][color]" class="w-full border-gray-300 rounded-md shadow-sm mt-1"></div>
            <div><label class="block text-sm font-medium text-gray-700">Ukuran</label><input type="text" name="variants[${variantIndex}][size]" class="w-full border-gray-300 rounded-md shadow-sm mt-1"></div>
            <div><label class="block text-sm font-medium text-gray-700">Stok</label><input type="number" name="variants[${variantIndex}][stock]" class="w-full border-gray-300 rounded-md shadow-sm mt-1" min="0"></div>
            <button type="button" class="remove-variant-btn bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-600 text-sm">Hapus</button>
        `;
                variantsContainer.appendChild(variantItem);
                variantIndex++;
            });

            variantsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-variant-btn')) {
                    e.target.closest('.variant-item').remove();
                }
            });

            // --- Logika Tagging ---
            const tagsInput = document.getElementById('tags-input');
            const tagsContainer = document.getElementById('tags-container');
            const tagsHidden = document.getElementById('tags-hidden');
            let tags = [];

            function renderTags() {
                tagsContainer.innerHTML = '';
                tagsHidden.value = tags.join(',');
                tags.forEach((tag, index) => {
                    const tagElement = document.createElement('div');
                    tagElement.className = 'tag';
                    tagElement.innerHTML = `
                <span>${tag}</span>
                <span class="tag-close" data-index="${index}">&times;</span>
            `;
                    tagsContainer.appendChild(tagElement);
                });
            }

            tagsInput.addEventListener('keyup', function(e) {
                if (e.key === ',' || e.key === 'Enter') {
                    const newTag = e.target.value.trim().replace(/,/g, '');
                    if (newTag.length > 1 && !tags.includes(newTag)) {
                        tags.push(newTag);
                    }
                    e.target.value = '';
                    renderTags();
                }
            });

            tagsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('tag-close')) {
                    const index = e.target.getAttribute('data-index');
                    tags.splice(index, 1);
                    renderTags();
                }
            });

            // --- Logika Image Preview ---
            function createImagePreviewer(inputId, containerId, multiple = false) {
                const input = document.getElementById(inputId);
                const container = document.getElementById(containerId);

                input.addEventListener('change', function(event) {
                    if (!multiple) {
                        container.innerHTML = '';
                    }
                    const files = event.target.files;
                    for (const file of files) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'image-preview-wrapper';
                            wrapper.innerHTML =
                                `<img src="${e.target.result}" class="image-preview"><span class="remove-image">&times;</span>`;
                            container.appendChild(wrapper);

                            wrapper.querySelector('.remove-image').addEventListener('click', () => {
                                wrapper.remove();
                                // Note: This only removes the preview, not the file from the input.
                                // A more complex solution is needed to manage the FileList object.
                            });
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            createImagePreviewer('main_image', 'main-image-preview-container');
            createImagePreviewer('gallery_images', 'gallery-preview-container', true);
        });
    </script>
@endpush
