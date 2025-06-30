@extends('layouts.mitra')

@section('title', 'Edit Produk: ' . $product->name)

@push('styles')
    <!-- Trix Editor CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
    <style>
        /* Gaya untuk area upload media */
        .media-upload-area {
            @apply border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 flex flex-col items-center justify-center min-h-[150px] relative;
        }

        /* Gaya untuk thumbnail pratinjau */
        .image-preview-thumbnail {
            @apply max-w-full max-h-[120px] object-contain rounded-lg;
        }

        /* Gaya untuk area drag handle varian */
        .drag-handle {
            @apply cursor-grab text-gray-500 hover:text-gray-700 active:cursor-grabbing;
        }
    </style>
@endpush

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Produk: {{ $product->name }}</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Oops!</strong> Ada masalah dengan input Anda.
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mitra.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" required
                   placeholder="Short sleeve t-shirt">
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
            <input id="description" type="hidden" name="description" value="{{ old('description', $product->description) }}">
            <trix-editor input="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"></trix-editor>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Media:</label>
            <div id="mediaUploadArea" class="media-upload-area">
                {{-- Preview Gambar Utama --}}
                <div id="imagePreviewWrapper" class="relative group hidden w-full h-full flex items-center justify-center">
                    <img id="imagePreview" src="#" alt="Pratinjau Media" class="image-preview-thumbnail">
                    {{-- Tombol Hapus --}}
                    <button type="button" onclick="removeSelectedImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    {{-- Overlay Loading --}}
                    <div id="loadingOverlay" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white rounded-lg hidden">
                        <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Area tombol dan teks default --}}
                <div id="defaultUploadContent" class="flex flex-col items-center justify-center w-full h-full">
                    <div class="flex space-x-4 mb-2">
                        <button type="button" onclick="document.getElementById('thumbnail').click()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition duration-300">
                            Upload new
                        </button>
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg text-sm transition duration-300">
                            Select existing
                        </button>
                    </div>
                    <p class="text-gray-600 text-sm">Accepts images, videos, or 3D models</p>
                </div>

                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" onchange="handleFileSelection(event)" class="hidden">
                {{-- Hidden input to mark for thumbnail removal if remove button clicked --}}
                <input type="hidden" name="remove_thumbnail_flag" id="removeThumbnailFlag" value="0">
            </div>
            @error('thumbnail')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Bagian form lain yang tidak berubah --}}
        <div class="mb-4">
            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
            <select name="category_id" id="category_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Harga:</label>
            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror" required>
            @error('price')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">Stok:</label>
            <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror" required>
            @error('stock')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="product_discount" class="block text-gray-700 text-sm font-bold mb-2">Diskon Produk (%):</label>
            <input type="number" name="product_discount" id="product_discount" value="{{ old('product_discount', $product->product_discount ?? 0) }}" min="0" max="100"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('product_discount') border-red-500 @enderror">
            @error('product_discount')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
            <select name="status" id="status"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- BAGIAN VARIAN DINAMIS --}}
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8 mt-8 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Varian</h2>
            <p class="text-gray-600 text-sm mb-4">
                Definisikan opsi produk seperti Ukuran, Warna, dll. Anda dapat menambahkan beberapa opsi dan nilai untuk setiap opsi.
                Contoh: Opsi: Size, Nilai: S, M, L.
            </p>
            <div id="variant-options-container" class="space-y-4">
                {{-- Template untuk opsi varian --}}
                <div class="variant-option-template hidden bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="drag-handle text-gray-500 hover:text-gray-700 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </span>
                            <label class="block text-gray-700 text-sm font-bold">Opsi Varian</label>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 text-sm font-semibold" onclick="removeVariantOption(this)">
                            Hapus
                        </button>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Opsi:</label>
                        <input type="text" name="options[0][name]"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Size, Color">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nilai Opsi (Pisahkan dengan koma):</label>
                        <input type="text" name="options[0][values]"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: S, M, L atau Red, Blue">
                    </div>
                </div>
                {{-- Opsi Varian yang sudah ada (saat edit) --}}
                @if (isset($product) && $product->productOptions->isNotEmpty())
                    @foreach ($product->productOptions as $index => $option)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="drag-handle text-gray-500 hover:text-gray-700 text-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </span>
                                    <label class="block text-gray-700 text-sm font-bold">Opsi Varian</label>
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-700 text-sm font-semibold" onclick="removeVariantOption(this)">
                                    Hapus
                                </button>
                            </div>
                            <div class="mb-3">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Opsi:</label>
                                <input type="text" name="options[{{ $index }}][name]" value="{{ old('options.' . $index . '.name', $option->name) }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: Size, Color">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nilai Opsi (Pisahkan dengan koma):</label>
                                <input type="text" name="options[{{ $index }}][values]" value="{{ old('options.' . $index . '.values', $option->optionValues->pluck('value')->implode(', ')) }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: S, M, L atau Red, Blue">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" onclick="addVariantOption()" class="mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Opsi Lain</span>
            </button>
        </div>
        {{-- AKHIR BAGIAN VARIAN DINAMIS --}}

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300">
                Perbarui Produk
            </button>
            <a href="{{ route('mitra.products.show', $product->id) }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <!-- Trix Editor JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
    <script>
        // Fungsi handleFileSelection, removeSelectedImage tetap sama
        function handleFileSelection(event) {
            const fileInput = event.target;
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
            const defaultUploadContent = document.getElementById('defaultUploadContent');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const removeThumbnailFlag = document.getElementById('removeThumbnailFlag');


            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                // Tampilkan status loading
                loadingOverlay.classList.remove('hidden');
                imagePreviewWrapper.classList.remove('hidden'); // Tampilkan wrapper pratinjau segera
                defaultUploadContent.classList.add('hidden'); // Sembunyikan tombol dan teks default
                removeThumbnailFlag.value = '0'; // Pastikan flag hapus tidak aktif

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    // Sembunyikan loading setelah gambar dimuat
                    loadingOverlay.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                // Jika pemilihan dibatalkan atau tidak ada file yang dipilih, kembalikan ke kondisi awal
                removeSelectedImage();
            }
        }

        function removeSelectedImage() {
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
            const defaultUploadContent = document.getElementById('defaultUploadContent');
            const fileInput = document.getElementById('thumbnail');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const removeThumbnailFlag = document.getElementById('removeThumbnailFlag');


            imagePreview.src = '#'; // Reset src gambar
            imagePreviewWrapper.classList.add('hidden'); // Sembunyikan wrapper pratinjau
            defaultUploadContent.classList.remove('hidden'); // Tampilkan kembali tombol dan teks default
            fileInput.value = ''; // Hapus file dari input (penting untuk upload ulang)
            loadingOverlay.classList.add('hidden'); // Pastikan loading tersembunyi
            removeThumbnailFlag.value = '1'; // Tandai untuk dihapus saat submit
        }
        
        // --- JavaScript untuk Varian Dinamis ---
        let optionIndex = 0; // Untuk melacak indeks opsi varian

        function addVariantOption() {
            const container = document.getElementById('variant-options-container');
            const template = document.querySelector('.variant-option-template');
            const newOption = template.cloneNode(true); // Clone template

            newOption.classList.remove('hidden', 'variant-option-template'); // Hapus kelas tersembunyi dan template

            // Update name attributes with unique index
            newOption.querySelectorAll('[name^="options[0]"]').forEach(input => {
                input.name = input.name.replace('options[0]', `options[${optionIndex}]`);
                input.value = ''; // Kosongkan nilai untuk input baru
            });

            container.appendChild(newOption);
            optionIndex++; // Increment index for the next option
        }

        function removeVariantOption(button) {
            button.closest('.bg-gray-50').remove(); // Hapus elemen opsi terdekat
            // Jika semua opsi dihapus, tambahkan satu kembali (opsional, tapi bisa jadi UX yang baik)
            if (document.querySelectorAll('#variant-options-container > .bg-gray-50').length === 0) {
                addVariantOption();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Untuk form 'create', $product->thumbnail_url akan kosong, jadi bagian ini tidak akan dieksekusi.
            // Ini lebih relevan untuk form 'edit'.
            const existingThumbnailUrl = "{{ $product->thumbnail_url ?? '' }}"; 
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
            const defaultUploadContent = document.getElementById('defaultUploadContent');

            if (existingThumbnailUrl && existingThumbnailUrl !== '#') {
                imagePreview.src = existingThumbnailUrl;
                imagePreviewWrapper.classList.remove('hidden');
                defaultUploadContent.classList.add('hidden');
            }

            // Inisialisasi optionIndex berdasarkan jumlah opsi yang ada (untuk edit form)
            const existingOptionsCount = document.querySelectorAll('#variant-options-container > .bg-gray-50').length;
            if (existingOptionsCount > 0) {
                optionIndex = existingOptionsCount;
            } else {
                // Untuk form 'create' atau jika tidak ada opsi yang ada, tambahkan satu opsi kosong secara default
                addVariantOption();
            }
        });
    </script>
@endpush
