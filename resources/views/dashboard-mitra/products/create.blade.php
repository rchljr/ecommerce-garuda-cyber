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
            min-height: 42px; /* Agar tidak terlalu kecil jika belum ada tag */
            align-items: center;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            background-color: #3b82f6; /* Tailwind blue-600 */
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
            overflow: hidden; /* Pastikan gambar tidak keluar dari batas */
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
            background-color: #ef4444; /* Tailwind red-500 */
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
            color: #ef4444; /* Warna merah */
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
            background-color: #e2e8f0; /* bg-gray-200 */
            color: #2d3748; /* text-gray-800 */
            padding: 0.25rem 0.75rem;
            border-radius: 9999px; /* rounded-full */
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .option-value-tag button {
            margin-left: 0.5rem;
            background: none;
            border: none;
            color: #4a5568; /* text-gray-600 */
            cursor: pointer;
            font-weight: bold;
            line-height: 1;
            padding: 0;
        }

        .option-value-tag button:hover {
            color: #e53e3e; /* text-red-600 */
        }
        
        /* Styling for the file input button inside table cell */
        /* ini untuk input file yang disembunyikan dan diganti dengan label */
        .file-input-button {
            display: inline-block;
            background-color: #eff6ff; /* blue-50 */
            color: #1d4ed8; /* blue-700 */
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
            max-width: 100%; /* Agar tidak melebihi sel tabel */
            box-sizing: border-box; /* Agar padding tidak menyebabkan overflow */
        }
        .file-input-button:hover {
            background-color: #dbeafe; /* blue-100 */
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

                {{-- Kolom Kiri (Informasi Utama) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Nama & Deskripsi --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Informasi Dasar</h2>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1 required-label">Nama
                                Produk</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi
                                Singkat</label>
                            <textarea id="short_description" name="short_description" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm">{{ old('short_description') }}</textarea>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Lengkap
                            </label>
                            {{-- Disarankan menggunakan editor WYSIWYG seperti TinyMCE atau CKEditor di sini --}}
                            <textarea id="description" name="description" rows="8" class="w-full border-gray-300 rounded-md shadow-sm resize-y focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea> {{-- Menambahkan focus style --}}
                        </div>
                    </div>

                    {{-- Gambar & Galeri --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">Gambar Produk</h2>
                        <div class="mb-4">
                            <label for="main_image"
                                class="block text-sm font-medium text-gray-700 mb-1 required-label">Gambar Utama
                                (Thumbnail)</label>
                            <input type="file" id="main_image" name="main_image" class="w-full" accept="image/*"
                                required>
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
                                                        class="file-input-hidden"> {{-- Input file asli disembunyikan --}}
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
                                    {{ old('is_best_seller') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai Best Seller</span>
                            </label>
                            <label for="is_new_arrival" class="flex items-center">
                                <input type="checkbox" id="is_new_arrival" name="is_new_arrival" value="1"
                                    {{ old('is_new_arrival') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Tandai sebagai New Arrival</span>
                            </label>
                            <label for="is_hot_sale" class="flex items-center">
                                <input type="checkbox" id="is_hot_sale" name="is_hot_sale" value="1"
                                    {{ old('is_hot_sale') ? 'checked' : '' }}
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
                                        {{ old('sub_category_id') == $subCategory->id ? 'selected' : '' }}>
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
                            <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="tags-input" class="block text-sm font-medium text-gray-700 mb-1">Tag Produk
                                (pisahkan dengan koma)</label>
                            <input type="text" id="tags-input" class="w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="e.g., Baju, Musim Panas, Katun">
                            <input type="hidden" name="tags" id="tags-hidden" value="{{ old('tags') }}">
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
    <script src="//unpkg.com/alpinejs" defer></script> {{-- Pastikan Alpine.js dimuat --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika Tagging ---
            const tagsInput = document.getElementById('tags-input');
            const tagsContainer = document.getElementById('tags-container');
            const tagsHidden = document.getElementById('tags-hidden');
            let tags = [];

            // Inisialisasi tags dari old input atau nilai yang ada
            if (tagsHidden.value) {
                tags = tagsHidden.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
            }
            renderTags(); // Render tags awal

            function renderTags() {
                console.log('Rendering tags. Current tags:', tags); // Debugging line
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
                // Hanya tambahkan tag jika panjangnya lebih dari 1 karakter dan belum ada
                if (newTag.length > 1 && !tags.includes(newTag)) {
                    tags.push(newTag);
                    renderTags(); // Render segera setelah menambahkan tag baru
                }
                inputElement.value = ''; // Selalu kosongkan input setelah diproses
            }

            tagsInput.addEventListener('keyup', function(e) {
                if (e.key === ',' || e.key === 'Enter') {
                    processTagInput(e.target);
                }
            });

            tagsInput.addEventListener('blur', function(e) {
                processTagInput(e.target); // Proses tag saat input kehilangan fokus
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
                        return; // No files selected (e.g., user clicked cancel)
                    }

                    // Process files. For single, take only the first. For multiple, take all.
                    const filesToProcess = multiple ? Array.from(files) : [files[0]];

                    filesToProcess.forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'image-preview-wrapper';
                            wrapper.innerHTML =
                                `<img src="${e.target.result}" class="image-preview"><span class="remove-image" data-input-id="${inputId}" data-is-multiple="${multiple}">&times;</span>`;
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
                        const targetInput = document.getElementById(inputIdToClear);

                        if (targetInput) {
                            if (isMultipleInput) {
                                alert('Untuk menghapus gambar dari galeri, silakan unggah ulang semua gambar yang diinginkan.');
                                targetInput.value = ''; // Clear actual file input
                                container.innerHTML = ''; // Clear all previews
                            } else {
                                targetInput.value = ''; // Clear actual file input
                                e.target.closest('.image-preview-wrapper').remove(); // Remove only this preview
                            }
                        } else {
                            e.target.closest('.image-preview-wrapper').remove(); // Fallback
                        }
                    }
                });
            }

            createImagePreviewer('main_image', 'main-image-preview-container');
            createImagePreviewer('gallery_images', 'gallery-preview-container', true);
        });

        // --- Alpine.js Data Handler for Flexible Variants ---
        document.addEventListener('alpine:init', () => {
            Alpine.data('productVariantsHandler', () => ({
                options: [],
                generatedVariants: [],
                nextOptionId: 1,

                init() {
                    const oldOptions = @json(old('options', []));
                    if (oldOptions.length > 0) {
                        this.options = oldOptions.map((opt, index) => ({
                            id: this.nextOptionId++,
                            name: opt.name || '',
                            values: Array.isArray(opt.values) ? opt.values : (typeof opt
                                .values === 'string' ? opt.values.split(',').map(v => v
                                    .trim()).filter(v => v.length > 0) : [])
                        }));
                    } else {
                        this.addOption();
                    }
                    
                    const oldGeneratedVariants = @json(old('variants', []));
                    const oldVariantsMap = new Map();
                    if (oldGeneratedVariants.length > 0) {
                        oldGeneratedVariants.forEach(oldV => {
                            if (oldV.options) {
                                try {
                                    const parsedOptions = JSON.parse(oldV.options);
                                    const variantId = parsedOptions.map(c => c.name + ':' + c
                                        .value).join(';');
                                    oldVariantsMap.set(variantId, {
                                        modal_price: parseFloat(oldV.modal_price) || 0,
                                        profit_percentage: parseFloat(oldV.profit_percentage) || 0,
                                        stock: parseInt(oldV.stock) || 0,
                                        image_path_url: oldV.image_path_url || ''
                                    });
                                } catch (e) {
                                    console.error("Failed to parse old variant options JSON:",
                                        oldV.options, e);
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
                    const valuesToAdd = valueString.split(',').map(v => v.trim()).filter(v => v.length >
                        0);
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
                    const currentOptionGroups = this.options.filter(opt => opt.name && opt.values
                        .length > 0);

                    if (currentOptionGroups.length === 0) {
                        this.generatedVariants = [];
                        return;
                    }

                    // Function to get Cartesian product
                    const cartesianProduct = (arrays) => {
                        return arrays.reduce((a, b) =>
                            a.flatMap(d => b.map(e => [d, e].flat()))
                        );
                    };

                    const optionValueArrays = currentOptionGroups.map(opt =>
                        opt.values.map(val => ({
                            name: opt.name,
                            value: val
                        }))
                    );

                    if (optionValueArrays.length > 0) {
                        const combinations = cartesianProduct(optionValueArrays);

                        // Ensure combinations is always an array of arrays
                        const processedCombinations = combinations.map(combo => Array.isArray(combo) ?
                            combo : [combo]);

                        processedCombinations.forEach(combo => {
                            const variantName = combo.map(c => c.value).join(' / ');
                            const variantId = combo.map(c => c.name + ':' + c.value).join(
                                ';'); // Unique ID for matching

                            let modalPrice = 0;
                            let profitPercentage = 0;
                            let stock = 0;
                            let imagePathUrl = ''; // Untuk menyimpan URL gambar varian (jika ada)

                            // Prioritaskan old input (dari validasi gagal), lalu data yang sudah digenerate
                            if (this.oldVariantsMap && this.oldVariantsMap.has(variantId)) {
                                const oldData = this.oldVariantsMap.get(variantId);
                                modalPrice = parseFloat(oldData.modal_price) || 0;
                                profitPercentage = parseFloat(oldData.profit_percentage) || 0;
                                stock = parseInt(oldData.stock) || 0;
                                imagePathUrl = oldData.image_path_url || ''; // Ambil URL gambar dari old input
                            } else {
                                const existingVariant = this.generatedVariants.find(v => v
                                    .id === variantId);
                                if (existingVariant) {
                                    modalPrice = parseFloat(existingVariant.modal_price) || 0;
                                    profitPercentage = parseFloat(existingVariant.profit_percentage) || 0;
                                    stock = parseInt(existingVariant.stock) || 0;
                                    imagePathUrl = existingVariant.image_path_url || ''; // Ambil URL gambar dari varian yang sudah ada
                                }
                            }

                            // Pastikan modalPrice dan profitPercentage adalah angka sebelum perhitungan
                            const finalModalPrice = !isNaN(modalPrice) ? modalPrice : 0;
                            const finalProfitPercentage = !isNaN(profitPercentage) ? profitPercentage : 0;
                            const sellingPrice = finalModalPrice * (1 + (finalProfitPercentage / 100));

                            newGeneratedVariants.push({
                                id: variantId,
                                name: variantName,
                                modal_price: finalModalPrice,
                                profit_percentage: finalProfitPercentage,
                                selling_price: sellingPrice,
                                stock: stock,
                                options: combo, // Store the full option objects for backend
                                image_path_url: imagePathUrl // URL gambar varian
                            });
                        });
                    }

                    this.generatedVariants = newGeneratedVariants;
                },

                get totalStock() {
                    return this.generatedVariants.reduce((sum, variant) => sum + (variant.stock ||
                        0), 0);
                },

                formatRupiah(amount) { // Fungsi helper untuk format rupiah
                    if (typeof amount !== 'number' || isNaN(amount)) return 'Rp 0'; // Tangani NaN
                    return 'Rp ' + amount.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }
            }));
        });
    </script>
@endpush