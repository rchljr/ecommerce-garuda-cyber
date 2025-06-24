{{-- resources/views/admin/page_sections/partials/banner_form.blade.php --}}
{{-- Variabel $content akan berisi data lama (old() atau dari database saat edit) --}}
@extends('layouts.mitra')

@section('content')
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Konten Seksi Banner</h3>

    {{-- Banner Item 1 --}}
<div class="mb-6 p-4 border rounded-md bg-gray-50">
    <h4 class="text-lg font-semibold text-gray-800 mb-3">Banner Item 1</h4>
    <div class="mb-4">
        <label for="content_title_1" class="block text-gray-700 text-sm font-bold mb-2">Judul Banner 1:</label>
        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.title_1') border-red-500 @enderror" id="content_title_1" name="content[title_1]" value="{{ $content['title_1'] ?? '' }}" placeholder="Judul untuk banner pertama">
        @error('content.title_1')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="content_url_1" class="block text-gray-700 text-sm font-bold mb-2">URL Banner 1:</label>
        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.url_1') border-red-500 @enderror" id="content_url_1" name="content[url_1]" value="{{ $content['url_1'] ?? '' }}" placeholder="URL tujuan banner 1">
        @error('content.url_1')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="content_image_1" class="block text-gray-700 text-sm font-bold mb-2">Gambar Banner 1:</label>
        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('content.image_1') border-red-500 @enderror" id="content_image_1" name="content[image_1]">
        @if(isset($content['image_1']) && $content['image_1'])
            <div class="mt-2">
                Gambar Saat Ini: <img src="{{ asset('storage/' . $content['image_1']) }}" alt="Gambar Banner 1 Saat Ini" class="w-24 h-16 object-cover rounded shadow">
                <input type="hidden" name="content[current_image_1]" value="{{ $content['image_1'] }}">
            </div>
        @endif
        @error('content.image_1')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Banner Item 2 --}}
<div class="mb-6 p-4 border rounded-md bg-gray-50">
    <h4 class="text-lg font-semibold text-gray-800 mb-3">Banner Item 2</h4>
    <div class="mb-4">
        <label for="content_title_2" class="block text-gray-700 text-sm font-bold mb-2">Judul Banner 2:</label>
        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.title_2') border-red-500 @enderror" id="content_title_2" name="content[title_2]" value="{{ $content['title_2'] ?? '' }}" placeholder="Judul untuk banner kedua">
        @error('content.title_2')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="content_url_2" class="block text-gray-700 text-sm font-bold mb-2">URL Banner 2:</label>
        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.url_2') border-red-500 @enderror" id="content_url_2" name="content[url_2]" value="{{ $content['url_2'] ?? '' }}" placeholder="URL tujuan banner 2">
        @error('content.url_2')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="content_image_2" class="block text-gray-700 text-sm font-bold mb-2">Gambar Banner 2:</label>
        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('content.image_2') border-red-500 @enderror" id="content_image_2" name="content[image_2]">
        @if(isset($content['image_2']) && $content['image_2'])
            <div class="mt-2">
                Gambar Saat Ini: <img src="{{ asset('storage/' . $content['image_2']) }}" alt="Gambar Banner 2 Saat Ini" class="w-24 h-16 object-cover rounded shadow">
                <input type="hidden" name="content[current_image_2]" value="{{ $content['image_2'] }}">
            </div>
        @endif
        @error('content.image_2')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Banner Item 3 --}}
<div class="mb-6 p-4 border rounded-md bg-gray-50">
    <h4 class="text-lg font-semibold text-gray-800 mb-3">Banner Item 3</h4>
    <div class="mb-4">
        <label for="content_title_3" class="block text-gray-700 text-sm font-bold mb-2">Judul Banner 3:</label>
        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.title_3') border-red-500 @enderror" id="content_title_3" name="content[title_3]" value="{{ $content['title_3'] ?? '' }}" placeholder="Judul untuk banner ketiga">
        @error('content.title_3')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="content_url_3" class="block text-gray-700 text-sm font-bold mb-2">URL Banner 3:</label>
        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content.url_3') border-red-500 @enderror" id="content_url_3" name="content[url_3]" value="{{ $content['url_3'] ?? '' }}" placeholder="URL tujuan banner 3">
        @error('content.url_3')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="content_image_3" class="block text-gray-700 text-sm font-bold mb-2">Gambar Banner 3:</label>
        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('content.image_3') border-red-500 @enderror" id="content_image_3" name="content[image_3]">
        @if(isset($content['image_3']) && $content['image_3'])
            <div class="mt-2">
                Gambar Saat Ini: <img src="{{ asset('storage/' . $content['image_3']) }}" alt="Gambar Banner 3 Saat Ini" class="w-24 h-16 object-cover rounded shadow">
                <input type="hidden" name="content[current_image_3]" value="{{ $content['image_3'] }}">
            </div>
        @endif
        @error('content.image_3')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>
