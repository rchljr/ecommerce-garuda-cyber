<!--
=================================================================================
File 1: resources/views/mitra/editor/index.blade.php (Versi Diperbarui & Lengkap)
=================================================================================
-->
@extends('layouts.mitra')

@section('content')
    <div class="flex h-screen bg-gray-200">
        <!-- Panel Kiri: Kontrol -->
        <div class="w-1/3 p-6 bg-white overflow-y-auto shadow-lg">
            <div class="flex justify-between items-center mb-6 sticky top-0 bg-white py-4 z-10">
                <h3 class="text-xl font-bold text-gray-800">Editor Tampilan</h3>
            </div>

            <div class="space-y-4">

                <!-- Hero Section Controls -->
                <details class="border rounded group" open>
                    <summary class="bg-gray-100 p-3 font-semibold cursor-pointer group-open:border-b">
                        Hero Section
                    </summary>
                    <div class="p-4">
                        <!-- PERUBAHAN: Tombol untuk menambah slide baru -->
                        <div class="mb-4 text-center">
                            <a href="{{ route('mitra.heroes.index') }}"
                                class="w-full block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                + Tambah Slide Hero Baru
                            </a>
                            <p class="text-xs text-gray-500 mt-1">Anda akan diarahkan ke halaman Kelola Hero.</p>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-4">Mengedit slide hero pertama yang aktif. Untuk
                                menambah/menghapus slide, gunakan menu "Kelola Hero".</p>

                            {{-- Form Khusus untuk Hero
                            <form id="heroForm" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="hero_image" class="block text-sm font-medium text-gray-700">Gambar Latar
                                        Hero</label>
                                    <input type="file" id="hero_image" name="hero_image"
                                        data-target=".hero__items.set-bg" class="form-control-file mt-1">
                                </div>
                                <div>
                                    <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                                    <input type="text" id="subtitle" name="subtitle"
                                        data-target="#preview-hero-subtitle" value="{{ $hero->subtitle ?? '' }}"
                                        class="form-control">
                                </div>
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Judul
                                        Utama</label>
                                    <input type="text" id="title" name="title" data-target="#preview-hero-title"
                                        value="{{ $hero->title ?? '' }}" class="form-control">
                                </div>
                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <textarea id="description" name="description" data-target="#preview-hero-description" class="form-control">{{ $hero->description ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label for="button_text" class="block text-sm font-medium text-gray-700">Teks
                                        Tombol</label>
                                    <input type="text" id="button_text" name="button_text"
                                        data-target="#preview-hero-button-text" value="{{ $hero->button_text ?? '' }}"
                                        class="form-control">
                                </div>
                                <div>
                                    <label for="button_url" class="block text-sm font-medium text-gray-700">URL
                                        Tombol</label>
                                    <input type="url" id="button_url" name="button_url" data-target-href=".primary-btn"
                                        value="{{ $hero->button_url ?? '' }}" class="form-control"
                                        placeholder="https://...">
                                </div>
                                <div class="text-right">
                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan
                                        Hero</button>
                                </div>
                            </form> --}}
                        </div>
                </details>

                <!-- Banner Section Controls -->
                <details class="border rounded group">
                    <summary class="bg-gray-100 p-3 font-semibold cursor-pointer group-open:border-b">
                        Banner Section
                    </summary>
                    <div class="mb-4 text-center">
                            <a href="{{ route('mitra.banners.index') }}"
                                class="w-full block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                + Tambah Slide Banner Baru
                            </a>
                            <p class="text-xs text-gray-500 mt-1">Anda akan diarahkan ke halaman Kelola Banner.</p>
                        </div>
                </details>

                <!-- Pengaturan Umum & Kontak -->
                <details class="border rounded group">
                    <summary class="bg-gray-100 p-3 font-semibold cursor-pointer group-open:border-b">
                        Pengaturan Umum & Kontak
                    </summary>
                    <div class="mb-4 text-center">
                            <a href="{{ route('mitra.contacts') }}"
                                class="w-full block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Atur Halaman Kontak
                            </a>
                            <p class="text-xs text-gray-500 mt-1">Anda akan diarahkan ke halaman Kelola Halaman Kontak.</p>
                        </div>
                </details>

            </div>
        </div>

        <!-- Panel Kanan: Preview Live -->
        <div class="w-2/3 bg-gray-50">
            <iframe id="previewFrame" src="{{ route('tenant.home', ['subdomain' => $shop->subdomain->subdomain_name]) }}"
                class="w-full h-full border-0"></iframe>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Logika JavaScript untuk Live Preview & AJAX Save (Versi Lengkap)
        $(document).ready(function() {
            const iframe = $('#previewFrame');

            // Fungsi untuk memperbarui preview
            function updatePreview(inputElement) {
                const targetSelector = $(inputElement).data('target');
                const targetHrefSelector = $(inputElement).data('target-href');
                const targetImgSelector = $(inputElement).data('target-img');
                const newValue = $(inputElement).val();
                const inputType = $(inputElement).attr('type');

                if (targetSelector) {
                    const targetElement = iframe.contents().find(targetSelector);
                    if (inputType === 'color') {
                        targetElement.css('background-color', newValue);
                        targetElement.css('border-color', newValue);
                    } else {
                        targetElement.text(newValue);
                    }
                }

                if (targetHrefSelector) {
                    iframe.contents().find(targetHrefSelector).attr('href', newValue);
                }

                // Live preview untuk gambar
                if (targetImgSelector && inputElement.files && inputElement.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const targetElement = iframe.contents().find(targetImgSelector);
                        // Cek apakah targetnya adalah background atau <img>
                        if (targetElement.is('img')) {
                            targetElement.attr('src', e.target.result);
                        } else {
                            targetElement.css('background-image', 'url(' + e.target.result + ')');
                        }
                    }
                    reader.readAsDataURL(inputElement.files[0]);
                }
            }

            // Listener untuk semua input di form editor
            $('#editorForm input, #editorForm textarea').on('input change keyup', function() {
                updatePreview(this);
            });

            // Fungsi helper untuk AJAX
            function handleFormSubmit(formId, url, successMessage) {
                $('#' + formId).on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            alert(successMessage);
                            iframe.attr('src', iframe.attr('src'));
                        },
                        error: function() {
                            alert('Gagal menyimpan perubahan. Periksa kembali input Anda.');
                        }
                    });
                });
            }

            // Panggil helper untuk setiap form
            handleFormSubmit('heroForm', '{{ route('mitra.editor.update.hero') }}',
                'Pengaturan Hero berhasil disimpan!');
            handleFormSubmit('settingsForm', '{{ route('mitra.editor.update.settings') }}',
                'Pengaturan Umum berhasil disimpan!');
        });
    </script>
@endpush
