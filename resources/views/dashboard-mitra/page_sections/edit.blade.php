@extends('layouts.mitra')

@section('title', 'Edit Seksi ' . $section->section_type . ' untuk ' . $page->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Seksi "{{ Str::title(str_replace('_', ' ', $section->section_type)) }}" untuk "{{ $page->name }}"</h1>

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

        <form action="{{ route('mitra.pages.sections.update', [$page, $section]) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="section_type" class="block text-gray-700 text-sm font-bold mb-2">Tipe Seksi <span class="text-red-500">*</span>:</label>
                <select name="section_type" id="section_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" disabled>
                    {{-- Tipe seksi tidak bisa diubah setelah dibuat --}}
                    @foreach($sectionTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('section_type', $section->section_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                {{-- Kirim nilai section_type via hidden input agar tetap bisa diproses di controller --}}
                <input type="hidden" name="section_type" value="{{ $section->section_type }}">
            </div>

            <div class="mb-4">
                <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Urutan:</label>
                <input type="number" name="order" id="order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order') border-red-500 @enderror" value="{{ old('order', $section->order) }}">
                @error('order')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 flex items-center">
    {{-- Ini akan memastikan nilai '0' terkirim jika checkbox TIDAK dicentang --}}
    <input type="hidden" name="is_active" value="0"> 
    <input type="checkbox" name="is_active" id="is_active" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
    <label for="is_active" class="ml-2 block text-gray-900 text-sm">Is Active</label>
</div>

            <hr class="my-6 border-gray-300">

            <div id="dynamic-content-form">
                {{-- Form fields for each section type will be loaded here via JavaScript --}}
                {{-- Load existing partial on initial page load --}}
                @include('dashboard-mitra.page_sections.partials.' . $section->section_type . '_form', ['content' => old('content', $section->content ?? [])])
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Update Seksi
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

            // This function is for dynamic loading, but for edit, the initial form is loaded via include
            // However, it's good to keep this logic if you have complex interactions.
            function loadSectionForm(sectionType, currentContent = {}) {
                if (!sectionType) {
                    dynamicContentForm.innerHTML = '';
                    return;
                }

                const url = `{{ url('dashboard-mitra/get-section-form-partial') }}/${sectionType}`;
                
                axios.get(url, { 
                    params: { 
                        content: JSON.stringify(currentContent) 
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

            // For edit page, the section_type is disabled, so no 'change' listener is needed.
            // The initial form is loaded directly by PHP include.
            // If there's validation error and old input, we need to ensure content is passed correctly.
            const initialSectionType = sectionTypeSelect.value;
            // Use old('content') if validation failed, otherwise use existing $section->content
            const initialContent = @json(old('content', $section->content ?? [])); 
            
            // Re-load the form to ensure old input is populated correctly after a validation error
            // This is especially useful if content fields are dynamically generated
            if (initialSectionType) {
                 loadSectionForm(initialSectionType, initialContent);
            }
        });
    </script>
    @endpush
