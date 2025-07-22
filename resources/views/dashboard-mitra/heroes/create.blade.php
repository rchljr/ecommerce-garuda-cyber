@extends('layouts.mitra')

@section('title', 'Tambah Item Hero Baru')

@push('styles')
    <style>
        /* Styles for image preview */
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .image-preview-wrapper {
            position: relative;
            width: 120px; /* Ukuran preview */
            height: 120px; /* Ukuran preview */
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
            z-index: 10; /* Pastikan tombol hapus di atas gambar */
        }

        /* Style untuk penanda wajib isi */
        .required-label::after {
            content: '*';
            color: #ef4444; /* Warna merah */
            margin-left: 4px;
        }
    </style>
@endpush

@section('content')
<div class="bg-white shadow-xl rounded-xl p-6 sm:p-8 md:p-10 max-w-screen-md w-full mx-auto">
    <div class="flex items-center justify-center mb-8 relative">
        <h1 class="text-3xl font-extrabold text-gray-900 text-center">Tambah Item Hero Baru</h1>
        <button type="button" id="info-icon-btn" class="ml-3 text-blue-500 hover:text-blue-700 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </button>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">
            <strong class="font-bold">Oops!</strong> Ada masalah dengan input Anda:
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mitra.heroes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            <div class="col-span-1 md:col-span-2">
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1 required-label">Judul Hero:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-500 @enderror"
                       placeholder="Fall - Winter Collections 2030" required>
                @error('title')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subtitle" class="block text-sm font-semibold text-gray-700 mb-1">Subjudul (Opsional):</label>
                <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('subtitle') border-red-500 @enderror"
                       placeholder="Summer Collection">
                @error('subtitle')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="button_text" class="block text-sm font-semibold text-gray-700 mb-1">Teks Tombol (Opsional):</label>
                <input type="text" name="button_text" id="button_text" value="{{ old('button_text') }}"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('button_text') border-red-500 @enderror"
                       placeholder="Shop now">
                @error('button_text')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="button_url" class="block text-sm font-semibold text-gray-700 mb-1">URL Tombol (Opsional):</label>
                <input type="url" name="button_url" id="button_url" value="{{ old('button_url') }}"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('button_url') border-red-500 @enderror"
                       placeholder="https://example.com/shop">
                @error('button_url')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi (Opsional):</label>
                <textarea name="description" id="description" rows="4"
                          class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-500 @enderror"
                          placeholder="A specialist label creating luxury essentials...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-1 required-label">Gambar Hero:</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Unggah file</span>
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" required>
                            </label>
                            <p class="pl-1">atau tarik dan lepas</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            PNG, JPG, GIF hingga 5MB
                        </p>
                    </div>
                </div>
                {{-- Kontainer untuk menampilkan preview gambar yang diunggah --}}
                <div id="hero-image-preview-container" class="image-preview-container"></div>
                @error('image')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="order" class="block text-sm font-semibold text-gray-700 mb-1 required-label">Urutan Tampilan:</label>
                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('order') border-red-500 @enderror" required>
                @error('order')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center mt-6 md:mt-0">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Aktifkan Hero Ini</label>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-10">
            <a href="{{ route('mitra.heroes.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                Simpan Hero
            </button>
        </div>
    </form>
</div>

<div id="tutorial-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 hidden">
    {{-- Container modal, menggunakan flex-col dan max-h-screen untuk mengatur tinggi --}}
    <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 w-full max-w-2xl mx-auto m-4 relative flex flex-col max-h-[90vh]">
        <button type="button" id="close-modal-btn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-3xl font-light leading-none focus:outline-none" aria-label="Close modal">
            &times;
        </button>
        <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b pb-2 flex-shrink-0">Informasi Gambar Hero</h2>
        
        {{-- Konten modal yang bisa di-scroll --}}
        <div class="prose max-w-none text-gray-700 overflow-y-auto flex-grow">
            <p>Tonton video GIF panduan ini untuk memahami rekomendasi optimal untuk gambar Hero Anda.</p>
            
            {{-- GIF untuk panduan Hero --}}
            <div class="w-full bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center p-2 mb-4">
                {{-- Gunakan jalur yang sesuai untuk GIF tutorial Hero Anda --}}
                <img src="{{ asset('images/Tutorial_Hero.gif') }}" alt="Panduan Gambar Hero" class="max-w-full h-auto rounded-md">
            </div>

            <p class="text-sm text-gray-600 mt-4">
                GIF ini akan secara otomatis memutar dan mengulang. Berikut detail rekomendasi gambar Hero:
            </p>
            <ul class="list-disc list-inside space-y-2 mt-4 border-t pt-4">
                <li>
                    <strong>Rasio Aspek Ideal:</strong> Sekitar <strong>2.4:1</strong> atau <strong>16:9</strong> (lebar lebih besar dari tinggi). Ini membantu gambar terlihat baik di layar lebar.
                </li>
                <li>
                    <strong>Resolusi Minimum:</strong> <strong>1920px lebar x 800px tinggi</strong>. Resolusi ini memastikan gambar tidak terlihat buram pada layar definisi tinggi.
                </li>
                <li>
                    <strong>Resolusi Optimal:</strong> <strong>2560px lebar x 1080px tinggi</strong> atau lebih tinggi. Resolusi ini memberikan kualitas visual terbaik dan detail yang tajam.
                </li>
                <li>
                    <strong>Ukuran File:</strong> Maksimal <strong>5MB</strong> untuk memastikan kecepatan loading halaman yang baik. Ukuran file yang besar dapat memperlambat situs Anda.
                </li>
                <li>
                    <strong>Konten Gambar:</strong> Hindari menempatkan teks penting atau elemen kritis di tepi gambar. Di perangkat dengan ukuran layar berbeda, area tepi gambar mungkin terpotong.
                </li>
            </ul>
            <p class="text-gray-700 mt-4">Pastikan gambar Anda berkualitas tinggi dan relevan dengan promosi utama toko Anda untuk menarik perhatian pengunjung.</p>
        </div>
        {{-- Footer modal, pastikan tidak menyusut --}}
        <div class="flex justify-end mt-6 flex-shrink-0">
            {{-- <button type="button" id="close-modal-btn" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none">Tutup</button> --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Logika Modal Informasi Gambar Hero ---
        const infoIconBtn = document.getElementById('info-icon-btn');
        const tutorialModal = document.getElementById('tutorial-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');

        if (infoIconBtn && tutorialModal && closeModalBtn) {
            infoIconBtn.addEventListener('click', () => {
                tutorialModal.classList.remove('hidden');
            });

            closeModalBtn.addEventListener('click', () => {
                tutorialModal.classList.add('hidden');
            });

            // Tutup modal jika klik di luar konten modal
            tutorialModal.addEventListener('click', (e) => {
                if (e.target === tutorialModal) {
                    tutorialModal.classList.add('hidden');
                }
            });
        }

        // --- Logika Image Preview untuk Gambar Hero ---
        function createImagePreviewer(inputId, containerId) {
            const input = document.getElementById(inputId);
            const container = document.getElementById(containerId);

            if (input && container) {
                input.addEventListener('change', function(event) {
                    container.innerHTML = ''; // Bersihkan preview yang ada
                    const files = event.target.files;
                    if (files.length > 0) {
                        const file = files[0]; // Hero hanya punya 1 gambar utama
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'image-preview-wrapper'; // Gunakan kelas CSS yang sudah ada
                            wrapper.innerHTML = `
                                <img src="${e.target.result}" class="image-preview">
                                <span class="remove-image" data-input-id="${inputId}">&times;</span>
                            `;
                            container.appendChild(wrapper);
                        }
                        reader.readAsDataURL(file);
                    }
                });

                // Tambahkan event listener untuk tombol hapus pada preview
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-image')) {
                        const inputIdToRemove = e.target.getAttribute('data-input-id');
                        const targetInput = document.getElementById(inputIdToRemove);
                        if (targetInput) {
                            targetInput.value = ''; // Reset input file
                        }
                        e.target.closest('.image-preview-wrapper').remove();
                    }
                });
            }
        }

        // Panggil fungsi createImagePreviewer untuk input gambar Hero
        createImagePreviewer('image', 'hero-image-preview-container');
    });
</script>
@endpush