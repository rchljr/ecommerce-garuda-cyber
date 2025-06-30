@extends('layouts.mitra')

@section('title', 'Tambah Seksi Baru ke ' . $page->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Seksi Baru ke "{{ $page->name }}"</h1>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">Ada masalah dengan input Anda.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mitra.pages.sections.store', $page) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            <div class="mb-4">
                <label for="section_type" class="block text-gray-700 text-sm font-bold mb-2">Tipe Seksi <span class="text-red-500">*</span>:</label>
                <select name="section_type" id="section_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Pilih tipe seksi</option>
                    @foreach($sectionTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('section_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('section_type')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Urutan:</label>
                <input type="number" name="order" id="order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order') border-red-500 @enderror" value="{{ old('order', $page->sections->max('order') + 1) }}">
                @error('order')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

          <div class="mb-4 flex items-center">
    {{-- Ini akan memastikan nilai '0' terkirim jika checkbox TIDAK dicentang --}}
    <input type="hidden" name="is_active" value="0"> 
    <input type="checkbox" name="is_active" id="is_active" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
    <label for="is_active" class="ml-2 block text-gray-900 text-sm">Aktif</label>
        </div>

            <hr class="my-6 border-gray-300">

            <div id="dynamic-content-form">
                {{-- Form fields for each section type will be loaded here via JavaScript --}}
                @if(old('section_type'))
                    {{-- Load existing partial if validation fails and old input exists --}}
                    @include('dashboard-mitra.page_sections.partials.' . old('section_type') . '_form', ['content' => old('content', [])])
                @endif
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Simpan Seksi
                </button>
                <a href="{{ route('mitra.pages.sections.index', $page) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sectionTypeSelect = document.getElementById('section_type');
            const dynamicContentForm = document.getElementById('dynamic-content-form');

            function loadSectionForm(sectionType, currentContent = {}) {
                if (!sectionType) {
                    dynamicContentForm.innerHTML = '';
                    return;
                }

                // Construct URL for AJAX request to fetch the partial view
                const url = `{{ url('mitra/get-section-form-partial') }}/${sectionType}`;
                
                // Use Axios to make an AJAX request
                axios.get(url, { 
                    params: { 
                        content: JSON.stringify(currentContent) // Stringify content to pass as URL param
                    } 
                })
                .then(response => {
                    dynamicContentForm.innerHTML = response.data;
                })
                .catch(error => {
                    console.error('Error loading section form:', error);
                    dynamicContentForm.innerHTML = '<p class="text-red-500">Gagal memuat form seksi. Pastikan partial view ada dan rute AJAX benar.</p>';
                });
            }

            sectionTypeSelect.addEventListener('change', function() {
                loadSectionForm(this.value);
            });

            // Load initial form if old input or existing section data is present (for edit/validation retry)
            const initialSectionType = sectionTypeSelect.value;
            // For 'create' view, $section is not defined, so default to empty array
            const initialContent = @json(old('content', [])); 
            if (initialSectionType) {
                loadSectionForm(initialSectionType, initialContent);
            }
        });
    </script>
    @endpush
