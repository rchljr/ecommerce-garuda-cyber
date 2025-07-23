{{-- Diasumsikan Anda memiliki layout untuk dashboard admin --}}
@extends('layouts.mitra')

@section('title', 'Edit Produk: ' . $product->name)

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
            min-height: 42px; /* Agar tidak terlalu kecil jika belum ada tag */
            align-items: center;
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
            overflow: hidden;
            border-radius: 0.375rem;
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
            z-index: 10;
        }

        /* Style untuk penanda wajib isi */
        .required-label::after {
            content: '*';
            color: #ef4444;
            margin-left: 4px;
        }

        /* Tambahan CSS untuk tampilan varian fleksibel */
        .option-group {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f9f9f9;
        }

        .option-value-tag {
            display: inline-flex;
            align-items: center;
            background-color: #e2e8f0;
            color: #2d3748;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .option-value-tag button {
            margin-left: 0.5rem;
            background: none;
            border: none;
            color: #4a5568;
            cursor: pointer;
            font-weight: bold;
            line-height: 1;
            padding: 0;
        }

        .option-value-tag button:hover {
            color: #e53e3e;
        }

        /* Styling for the file input button inside table cell */
        .file-input-button {
            display: inline-block;
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            box-sizing: border-box;
        }

        .file-input-button:hover {
            background-color: #dbeafe;
        }

        /* Sembunyikan input file asli */
        .file-input-hidden {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Produk: {{ $product->name }}</h1>

        {{-- Menampilkan pesan error validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form utama --}}
        <form action="{{ route('mitra.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Kolom Kiri (Info Utama) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Nama & Deskripsi --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Informasi Dasar</h2>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi
                                Singkat</label>
                            <textarea id="short_description" name="short_description" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi
                                Lengkap</label>
                            <textarea id="description" name="description" rows="8"
                                class="w-full border-gray-300 rounded-md shadow-sm resize-y">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    {{-- Gambar & Galeri --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Gambar Produk</h2>
                        <div class="mb-4">
                            <label for="main_image" class="block text-sm font-medium text-gray-700 mb-1">Ganti Gambar
                                Utama (Opsional)</label>
                            <input type="file" id="main_image" name="main_image" class="w-full" accept="image/*">
                            <div id="main-image-preview-container" class="image-preview-container">
                                {{-- Preview gambar utama yang sudah ada --}}
                                @if ($product->main_image)
                                    <div class="image-preview-wrapper" data-image-path="{{ $product->main_image }}">
                                        <img src="{{ Storage::url($product->main_image) }}" class="image-preview"
                                            alt="Gambar Utama">
                                        <span class="remove-image" data-input-id="main_image" data-is-existing="true">&times;</span>
                                    </div>
                                @endif
                            </div>
                            <input type="hidden" name="main_image_removed" id="main-image-removed-flag" value="0"> {{-- Flag untuk menandai penghapusan --}}
                        </div>
                        <div>
                            <label for="gallery_images" class="block text-sm font-medium text-gray-700 mb-1">Tambah Gambar
                                Galeri (Opsional)</label>
                            <input type="file" id="gallery_images" name="gallery_images[]" class="w-full"
                                accept="image/*" multiple>
                            <div id="gallery-preview-container" class="image-preview-container">
                                {{-- Preview gambar galeri yang sudah ada --}}
                                @foreach ($product->gallery_image_paths ?? [] as $path) {{-- Pastikan gallery_image_paths adalah array --}}
                                    <div class="image-preview-wrapper" data-image-path="{{ $path }}">
                                        <img src="{{ Storage::url($path) }}" class="image-preview"
                                            alt="Gambar Galeri">
                                        <span class="remove-image" data-input-id="gallery_images" data-is-existing="true" data-file-path="{{ $path }}">&times;</span>
                                    </div>
                                @endforeach
                            </div>
                            {{-- Hidden input untuk menyimpan path gambar galeri yang HANYA akan dihapus --}}
                            <input type="hidden" name="deleted_gallery_images[]" id="deleted-gallery-images" value="">
                        </div>
                    </div>

                    {{-- Varian Produk (Fleksibel dengan Alpine.js) --}}
                    <div class="bg-white p-6 rounded-lg shadow" x-data="productVariantsHandler()">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Varian Produk</h2>
                            <button type="button" @click="addOption()"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Tambah Opsi Varian
                            </button>
                        </div>

                        <div id="options-container" class="mb-8">
                            <template x-for="(option, optionIndex) in options" :key="option.id">
                                <div class="option-group mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label :for="`option-name-${option.id}`"
                                            class="block text-sm font-semibold text-gray-700">Nama Opsi Varian:</label>
                                        <button type="button" @click="removeOption(option.id)"
                                            class="text-red-500 hover:text-red-700 text-sm font-medium focus:outline-none">
                                            Hapus Opsi
                                        </button>
                                    </div>
                                    <input type="text" :name="`options[${optionIndex}][name]`" x-model="option.name"
                                        @input="generateVariants" :id="`option-name-${option.id}`"
                                        placeholder="e.g., Warna, Ukuran, Berat"
                                        class="mb-3 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required>

                                    <label :for="`option-values-${option.id}`"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Nilai Opsi (Pisahkan dengan
                                        koma):</label>
                                    <input type="text" :id="`option-values-${option.id}`"
                                        placeholder="e.g., Merah, Biru, Hijau"
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        @keyup.enter="addOptionValue(option, $event.target.value); $event.target.value=''"
                                        @blur="addOptionValue(option, $event.target.value); $event.target.value=''">
                                    <p class="text-xs text-gray-500 mt-1">Tekan Enter atau klik di luar untuk menambahkan
                                        nilai.</p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <template x-for="(value, valueIndex) in option.values" :key="valueIndex">
                                            <span class="option-value-tag">
                                                <span x-text="value"></span>
                                                <button type="button"
                                                    @click="removeOptionValue(option, valueIndex); generateVariants()">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-4">Daftar Varian Produk</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Varian</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Gambar Varian</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider required-label">
                                            Harga Modal (Rp)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider required-label">
                                            Profit (%)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Harga Jual (Rp)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider required-label">
                                            Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="generatedVariants.length === 0">
                                        <tr>
                                            <td colspan="6"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                Tambahkan opsi varian di atas untuk membuat daftar varian produk.
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(variant, index) in generatedVariants" :key="variant.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                                x-text="variant.name"></td>

                                            {{-- Input Gambar Varian --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{-- Menggunakan x-data terpisah untuk setiap gambar varian --}}
                                                <div x-data="{ fileInput: null, filePreview: variant.image_path_url || '' }" x-init="$watch('fileInput', (newVal) => {
                                                    if (newVal && newVal[0]) {
                                                        const reader = new FileReader();
                                                        reader.onload = (e) => { filePreview = e.target.result; };
                                                        reader.readAsDataURL(newVal[0]);
                                                    } else if (variant.image_path_url) {
                                                        filePreview = variant.image_path_url; // Revert to old if cleared
                                                    } else {
                                                        filePreview = '';
                                                    }
                                                });">
                                                    <input type="file" :id="`variant-image-${index}`"
                                                        :name="`variants[${index}][image_file]`"
                                                        x-ref="imageInput"
                                                        @change="fileInput = $event.target.files"
                                                        accept="image/*"
                                                        class="file-input-hidden">
                                                    <label :for="`variant-image-${index}`"
                                                        class="file-input-button">
                                                        <span x-text="fileInput && fileInput.length > 0 ? fileInput[0].name : (filePreview ? 'Ubah File' : 'Pilih File')"></span>
                                                    </label>
                                                    <template x-if="filePreview">
                                                        <div class="image-preview-wrapper mt-2">
                                                            <img :src="filePreview" class="image-preview"
                                                                alt="Varian Gambar">
                                                            <span class="remove-image"
                                                                @click="fileInput = null; filePreview = ''; $refs.imageInput.value = '';">&times;</span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </td>

                                            {{-- Input Harga Modal per Varian --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" :name="`variants[${index}][modal_price]`"
                                                    x-model.number="variant.modal_price" min="0" step="any"
                                                    required
                                                    class="w-32 block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    @input="generateVariants">
                                            </td>

                                            {{-- Input Persentase Keuntungan per Varian --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" :name="`variants[${index}][profit_percentage]`"
                                                    x-model.number="variant.profit_percentage" min="0" max="100"
                                                    step="0.01" required
                                                    class="w-24 block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    @input="generateVariants">
                                            </td>

                                            {{-- Tampilan Harga Jual Otomatis per Varian --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold"
                                                x-text="formatRupiah(variant.selling_price)"></td>

                                            {{-- Input Stok per Varian --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="number" :name="`variants[${index}][stock]`"
                                                    x-model.number="variant.stock" min="0" required
                                                    class="w-24 block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </td>

                                            {{-- Hidden input untuk menyimpan kombinasi opsi varian --}}
                                            <input type="hidden" :name="`variants[${index}][options]`"
                                                :value="JSON.stringify(variant.options)">
                                            {{-- Hidden input untuk mengirim nama varian gabungan ke backend --}}
                                            <input type="hidden" :name="`variants[${index}][name]`"
                                                :value="variant.name">
                                            {{-- Hidden input untuk mengirim ID varian (penting untuk edit/update) --}}
                                            <input type="hidden" :name="`variants[${index}][id]`"
                                                x-bind:value="variant.id">
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-sm text-gray-600 mt-4">Total Stok Tersedia: <span class="font-bold"
                                x-text="totalStock"></span></p>
                    </div>
                </div>

                {{-- Kolom Kanan (Pengaturan Tampilan, Organisasi, dan SKU) --}}
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Pengaturan Tampilan</h2>
                        <div class="space-y-4">
                            <label for="is_best_seller" class="flex items-center">
                                <input type="checkbox" id="is_best_seller" name="is_best_seller" value="1"
                                    {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai Best Seller</span>
                            </label>
                            <label for="is_new_arrival" class="flex items-center">
                                <input type="checkbox" id="is_new_arrival" name="is_new_arrival" value="1"
                                    {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai New Arrival</span>
                            </label>
                            <label for="is_hot_sale" class="flex items-center">
                                <input type="checkbox" id="is_hot_sale" name="is_hot_sale" value="1"
                                    {{ old('is_hot_sale', $product->is_hot_sale) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai Hot Sale</span>
                            </label>
                        </div>
                    </div>

                    {{-- Bagian Organisasi dan SKU --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Organisasi</h2>
                        <div class="mb-4">
                            <label for="sub_category_id"
                                class="block text-sm font-medium text-gray-700 mb-1 required-label">Sub
                                Kategori:</label>
                            <select name="sub_category_id" id="sub_category_id"
                                class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Pilih Sub Kategori</option>
                                @foreach ($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}"
                                        {{ old('sub_category_id', optional($product ?? null)->sub_category_id) == $subCategory->id ? 'selected' : '' }}>
                                        {{ $subCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU (Stock Keeping
                                Unit)</label>
                            <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="tags-input" class="block text-sm font-medium text-gray-700 mb-1">Tag Produk
                                (pisahkan dengan koma)</label>
                            <input type="text" id="tags-input" class="w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="e.g., Baju, Musim Panas, Katun">
                            <input type="hidden" name="tags" id="tags-hidden" value="{{ old('tags', $existingTags) }}"> {{-- Updated to use $existingTags --}}
                            <div id="tags-container" class="tag-container mt-2"></div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <button type="submit"
                            class="w-full bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 font-semibold">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script> {{-- Pastikan Alpine.js dimuat --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika Tagging ---
            const tagsInput = document.getElementById('tags-input');
            const tagsContainer = document.getElementById('tags-container');
            const tagsHidden = document.getElementById('tags-hidden');
            let tags = [];

            // Inisialisasi tags dari hidden input
            if (tagsHidden.value) {
                tags = tagsHidden.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
            }
            renderTags(); // Render tags awal

            function renderTags() {
                console.log('Rendering tags. Current tags:', tags);
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

            function processTagInput(inputElement) {
                const newTag = inputElement.value.trim().replace(/,/g, '');
                if (newTag.length > 1 && !tags.includes(newTag)) {
                    tags.push(newTag);
                    renderTags();
                }
                inputElement.value = '';
            }

            tagsInput.addEventListener('keyup', function(e) {
                if (e.key === ',' || e.key === 'Enter') {
                    processTagInput(e.target);
                }
            });

            tagsInput.addEventListener('blur', function(e) {
                processTagInput(e.target);
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

                if (!input || !container) {
                    console.warn(
                        `Image previewer: Input with ID '${inputId}' or container with ID '${containerId}' not found.`
                    );
                    return;
                }

                // Add a flag to prevent multiple listeners
                if (input.dataset.listenerAttached === 'true') {
                    return;
                }
                input.dataset.listenerAttached = 'true';

                input.addEventListener('change', function(event) {
                    container.innerHTML = ''; // Always clear the container on change event

                    const files = event.target.files;

                    if (files.length === 0) {
                        // Jika input file dikosongkan/cancel, dan bukan input multiple,
                        // pastikan preview existing tetap ada jika mode edit.
                        if (!multiple && input.dataset.initialPreviewPath) {
                            const initialPath = input.dataset.initialPreviewPath;
                             const wrapper = document.createElement('div');
                                wrapper.className = 'image-preview-wrapper';
                                wrapper.innerHTML =
                                    `<img src="${initialPath}" class="image-preview"><span class="remove-image" data-input-id="${inputId}" data-is-existing="true">&times;</span>`;
                                container.appendChild(wrapper);
                        } else if (!multiple) { // Jika tidak ada initial preview, kosongkan
                            container.innerHTML = '';
                        }
                        return; // Hentikan pemrosesan file jika tidak ada yang dipilih
                    }

                    const filesToProcess = multiple ? Array.from(files) : [files[0]];

                    filesToProcess.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'image-preview-wrapper';
                            wrapper.innerHTML =
                                `<img src="${e.target.result}" class="image-preview"><span class="remove-image" data-input-id="${inputId}" data-is-multiple="${multiple}" data-is-new="true">&times;</span>`; // Tambahkan data-is-new
                            container.appendChild(wrapper);
                        }
                        reader.readAsDataURL(file);
                    });
                });

                // Event listener for remove button on image previews
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-image')) {
                        const inputIdToClear = e.target.getAttribute('data-input-id');
                        const isMultipleInput = e.target.getAttribute('data-is-multiple') === 'true';
                        const isExistingImage = e.target.getAttribute('data-is-existing') === 'true'; // Untuk gambar yang sudah ada
                        const filePathToRemove = e.target.getAttribute('data-file-path'); // Untuk galeri
                        const targetInput = document.getElementById(inputIdToClear);

                        if (targetInput) {
                            if (isMultipleInput) {
                                if (isExistingImage && filePathToRemove) {
                                    // Untuk gambar galeri yang sudah ada: tandai untuk dihapus di backend
                                    const deletedGalleryInput = document.getElementById('deleted-gallery-images');
                                    if (deletedGalleryInput) {
                                        let deletedPaths = deletedGalleryInput.value ? JSON.parse(deletedGalleryInput.value) : [];
                                        deletedPaths.push(filePathToRemove);
                                        deletedGalleryInput.value = JSON.stringify(deletedPaths);
                                    }
                                }
                                // Hapus hanya preview ini
                                e.target.closest('.image-preview-wrapper').remove();
                                // Tidak mengosongkan input file multi, biarkan user mengunggah ulang jika ingin
                            } else { // Single image (main_image)
                                targetInput.value = ''; // Kosongkan input file
                                e.target.closest('.image-preview-wrapper').remove(); // Hapus preview
                                // Set flag untuk menandai bahwa gambar utama dihapus
                                const mainImageRemovedFlag = document.getElementById('main-image-removed-flag');
                                if(mainImageRemovedFlag) mainImageRemovedFlag.value = '1';
                            }
                        } else {
                            e.target.closest('.image-preview-wrapper').remove(); // Fallback
                        }
                    }
                });

                // Inisialisasi preview untuk gambar utama yang sudah ada
                // Ini akan dipanggil sekali saat DOMContentLoaded
                if (!multiple && input.dataset.initialPreviewPath) {
                    const initialPath = input.dataset.initialPreviewPath;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'image-preview-wrapper';
                    wrapper.innerHTML =
                        `<img src="${initialPath}" class="image-preview"><span class="remove-image" data-input-id="${inputId}" data-is-existing="true">&times;</span>`;
                    container.appendChild(wrapper);
                }
            }

            // Panggil untuk gambar utama
            const mainImageInput = document.getElementById('main_image');
            if (mainImageInput) {
                mainImageInput.dataset.initialPreviewPath = "{{ $product->main_image ? Storage::url($product->main_image) : '' }}";
            }
            createImagePreviewer('main_image', 'main-image-preview-container');

            // Panggil untuk galeri gambar (existing images handled by Blade loop)
            createImagePreviewer('gallery_images', 'gallery-preview-container', true);
        });

        // --- Alpine.js Data Handler for Flexible Variants ---
        document.addEventListener('alpine:init', () => {
            Alpine.data('productVariantsHandler', () => ({
                options: [], // [{ id: 1, name: 'Ukuran', values: ['S', 'M'] }]
                generatedVariants: [], // [{ id: 'S-Merah', name: 'S / Merah', price: 0, stock: 0, options: [{name: 'Ukuran', value: 'S'}, {name: 'Warna', value: 'Merah'}] }]
                nextOptionId: 1,

                init() {
                    // Load existing options from product for edit mode
                    // The raw data from controller is in $existingVariantsForAlpine
                    const productVariansData = @json($existingVariantsForAlpine ?? []);
                    
                    let existingOptions = [];
                    // Using a Map to collect unique options and their values
                    const optionNamesMap = new Map(); 

                    if (productVariansData.length > 0) {
                        productVariansData.forEach(varian => {
                            // Convert JSON string options to array of objects
                            let varianOptions = [];
                            try {
                                varianOptions = JSON.parse(varian.options); // Parse the 'options' JSON string
                            } catch (e) {
                                console.error("Failed to parse varian options JSON from DB:", varian.options, e);
                            }

                            if (Array.isArray(varianOptions)) {
                                varianOptions.forEach(opt => {
                                    if (opt.name && opt.value) { // Ensure name and value exist
                                        if (!optionNamesMap.has(opt.name)) {
                                            optionNamesMap.set(opt.name, {
                                                id: this.nextOptionId++,
                                                name: opt.name,
                                                values: new Set(),
                                            });
                                        }
                                        optionNamesMap.get(opt.name).values.add(opt.value);
                                    }
                                });
                            }
                        });

                        // Convert Set to Array and Map to Array for Alpine.js
                        existingOptions = Array.from(optionNamesMap.values()).map(opt => ({
                            id: opt.id,
                            name: opt.name,
                            values: Array.from(opt.values),
                        }));
                    }
                    this.options = existingOptions.length > 0 ? existingOptions : [{ id: this.nextOptionId++, name: '', values: [] }];
                    
                    // Inisialisasi generatedVariants dengan data yang sudah ada dari DB/old input
                    this.generatedVariants = productVariansData.map(varian => {
                        let parsedOptions = [];
                        try {
                            parsedOptions = JSON.parse(varian.options);
                        } catch (e) {
                            console.error("Failed to parse varian.options for generatedVariants init:", varian.options, e);
                        }
                        
                        return {
                            id: varian.id, // ID dari DB (untuk update/delete di backend)
                            name: varian.name,
                            modal_price: parseFloat(varian.modal_price) || 0,
                            profit_percentage: parseFloat(varian.profit_percentage) || 0,
                            selling_price: parseFloat(varian.selling_price) || 0, // Dapatkan dari DB jika ada, atau hitung ulang
                            stock: parseInt(varian.stock) || 0,
                            options: parsedOptions, // Ini adalah array objek
                            image_path_url: varian.image_path_url || '',
                            db_id: varian.id // Sama dengan id
                        };
                    });


                    // Load old input if validation failed on form submission, override existing generated
                    const oldGeneratedVariants = @json(old('variants', []));
                    const oldVariantsMap = new Map(); // Map untuk old input
                    if (oldGeneratedVariants.length > 0) {
                        oldGeneratedVariants.forEach(oldV => {
                            if (oldV.options) {
                                try {
                                    const parsedOptions = JSON.parse(oldV.options);
                                    const variantId = parsedOptions.map(c => c.name + ':' + c.value).join(';');
                                    oldVariantsMap.set(variantId, {
                                        id: oldV.id || variantId, // Gunakan ID dari old input jika ada
                                        name: oldV.name,
                                        modal_price: parseFloat(oldV.modal_price) || 0,
                                        profit_percentage: parseFloat(oldV.profit_percentage) || 0,
                                        stock: parseInt(oldV.stock) || 0,
                                        image_path_url: oldV.image_path_url || '',
                                        options: parsedOptions,
                                        db_id: oldV.id || null // Simpan ID DB jika ada
                                    });
                                } catch (e) {
                                    console.error("Failed to parse old variant options JSON for map:", oldV.options, e);
                                }
                            }
                        });
                    }
                    this.oldVariantsMap = oldVariantsMap;

                    this.generateVariants();
                },

                addOption() {
                    this.options.push({
                        id: this.nextOptionId++,
                        name: '',
                        values: []
                    });
                    this.generateVariants();
                },

                removeOption(optionId) {
                    this.options = this.options.filter(option => option.id !== optionId);
                    this.generateVariants();
                },

                addOptionValue(option, valueString) {
                    const valuesToAdd = valueString.split(',').map(v => v.trim()).filter(v => v.length > 0);
                    valuesToAdd.forEach(newValue => {
                        if (!option.values.includes(newValue)) {
                            option.values.push(newValue);
                        }
                    });
                    this.generateVariants();
                },

                removeOptionValue(option, index) {
                    option.values.splice(index, 1);
                    this.generateVariants();
                },

                generateVariants() {
                    let newGeneratedVariants = [];
                    const currentOptionGroups = this.options.filter(opt => opt.name && opt.values.length > 0);

                    if (currentOptionGroups.length === 0) {
                        this.generatedVariants = [];
                        return;
                    }

                    const cartesianProduct = (arrays) => {
                        return arrays.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
                    };

                    const optionValueArrays = currentOptionGroups.map(opt =>
                        opt.values.map(val => ({ name: opt.name, value: val }))
                    );

                    if (optionValueArrays.length > 0) {
                        const combinations = cartesianProduct(optionValueArrays);
                        const processedCombinations = combinations.map(combo => Array.isArray(combo) ? combo : [combo]);

                        processedCombinations.forEach(combo => {
                            const variantName = combo.map(c => c.value).join(' / ');
                            const variantId = combo.map(c => c.name + ':' + c.value).join(';');

                            let modalPrice = 0;
                            let profitPercentage = 0;
                            let stock = 0;
                            let imagePathUrl = '';
                            let dbId = null; 

                            // Prioritaskan old input (dari validasi gagal), lalu data dari generatedVariants yang sudah ada, lalu data dari productVariansData awal (DB)
                            if (this.oldVariantsMap && this.oldVariantsMap.has(variantId)) {
                                const oldData = this.oldVariantsMap.get(variantId);
                                modalPrice = oldData.modal_price;
                                profitPercentage = oldData.profit_percentage;
                                stock = oldData.stock;
                                imagePathUrl = oldData.image_path_url || '';
                                dbId = oldData.db_id || null;
                            } else {
                                // Cari di generatedVariants yang sudah ada (dari inisialisasi awal atau perubahan opsi sebelumnya)
                                const existingVariantInCurrentState = this.generatedVariants.find(v => {
                                    if (v.options && Array.isArray(v.options)) {
                                        const existingVariantIdKey = v.options.map(c => c.name + ':' + c.value).join(';');
                                        return existingVariantIdKey === variantId;
                                    }
                                    return false;
                                });

                                if (existingVariantInCurrentState) {
                                    modalPrice = existingVariantInCurrentState.modal_price;
                                    profitPercentage = existingVariantInCurrentState.profit_percentage;
                                    stock = existingVariantInCurrentState.stock;
                                    imagePathUrl = existingVariantInCurrentState.image_path_url || '';
                                    dbId = existingVariantInCurrentState.db_id || null;
                                } else {
                                    // Jika tidak ditemukan di old input atau current generated, coba cari di data awal dari DB
                                    const initialDbVariant = productVariansData.find(v => {
                                        let initialVarianOptions = [];
                                        try {
                                            initialVarianOptions = JSON.parse(v.options); // Ini aslinya string JSON dari PHP
                                        } catch(e) {
                                            initialVarianOptions = v.options; // Kalau sudah array, pakai langsung
                                        }
                                        if (initialVarianOptions && Array.isArray(initialVarianOptions)) {
                                            const initialVariantIdKey = initialVarianOptions.map(c => c.name + ':' + c.value).join(';');
                                            return initialVariantIdKey === variantId;
                                        }
                                        return false;
                                    });
                                    if (initialDbVariant) {
                                        modalPrice = parseFloat(initialDbVariant.modal_price) || 0;
                                        profitPercentage = parseFloat(initialDbVariant.profit_percentage) || 0;
                                        stock = parseInt(initialDbVariant.stock) || 0;
                                        imagePathUrl = initialDbVariant.image_path_url || initialDbVariant.image_path || '';
                                        dbId = initialDbVariant.id;
                                    }
                                }
                            }

                            const finalModalPrice = !isNaN(parseFloat(modalPrice)) ? parseFloat(modalPrice) : 0;
                            const finalProfitPercentage = !isNaN(parseFloat(profitPercentage)) ? parseFloat(profitPercentage) : 0;
                            const sellingPrice = finalModalPrice * (1 + (finalProfitPercentage / 100));

                            newGeneratedVariants.push({
                                id: dbId || variantId, // Gunakan ID dari DB jika ada, kalau tidak gunakan ID gabungan sementara
                                name: variantName,
                                modal_price: finalModalPrice,
                                profit_percentage: finalProfitPercentage,
                                selling_price: sellingPrice,
                                stock: stock,
                                options: combo,
                                image_path_url: imagePathUrl,
                                db_id: dbId // ID dari database, untuk backend sync
                            });
                        });
                    }

                    this.generatedVariants = newGeneratedVariants;
                },

                get totalStock() {
                    return this.generatedVariants.reduce((sum, variant) => sum + (variant.stock || 0), 0);
                },

                formatRupiah(amount) {
                    if (typeof amount !== 'number' || isNaN(amount)) return 'Rp 0';
                    return 'Rp ' + amount.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }
            }));
        });
    </script>
@endpush