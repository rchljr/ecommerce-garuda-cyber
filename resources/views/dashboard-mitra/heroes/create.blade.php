@extends('layouts.mitra')

@section('title', 'Tambah Item Hero Baru') {{-- Tetap 'Hero Baru' sesuai konteks kode yang Anda berikan --}}

@section('content')
<div class="bg-white shadow-xl rounded-xl p-6 sm:p-8 md:p-10 max-w-screen-md w-full mx-auto">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Tambah Item Hero Baru</h1>

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
        <!-- Judul Hero -->
        <div class="col-span-1 md:col-span-2">
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Hero:</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}"
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-500 @enderror"
                   placeholder="Fall - Winter Collections 2030" required>
            @error('title')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subtitle -->
        <div>
            <label for="subtitle" class="block text-sm font-semibold text-gray-700 mb-1">Subjudul (Opsional):</label>
            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('subtitle') border-red-500 @enderror"
                   placeholder="Summer Collection">
            @error('subtitle')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Button Text -->
        <div>
            <label for="button_text" class="block text-sm font-semibold text-gray-700 mb-1">Teks Tombol (Opsional):</label>
            <input type="text" name="button_text" id="button_text" value="{{ old('button_text') }}"
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('button_text') border-red-500 @enderror"
                   placeholder="Shop now">
            @error('button_text')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Button URL -->
        <div class="md:col-span-2">
            <label for="button_url" class="block text-sm font-semibold text-gray-700 mb-1">URL Tombol (Opsional):</label>
            <input type="url" name="button_url" id="button_url" value="{{ old('button_url') }}"
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('button_url') border-red-500 @enderror"
                   placeholder="https://example.com/shop">
            @error('button_url')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi (Opsional):</label>
            <textarea name="description" id="description" rows="4"
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-500 @enderror"
                      placeholder="A specialist label creating luxury essentials...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Upload -->
        <div class="md:col-span-2">
            <label for="image" class="block text-sm font-semibold text-gray-700 mb-1">Gambar Hero (Rekomendasi: 1920x800px):</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Unggah file</span>
                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                        </label>
                        <p class="pl-1">atau tarik dan lepas</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        PNG, JPG, GIF hingga 5MB
                    </p>
                </div>
            </div>
            @error('image')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Order -->
        <div>
            <label for="order" class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampilan:</label>
            <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('order') border-red-500 @enderror" required>
            @error('order')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Aktif Checkbox -->
        <div class="flex items-center mt-6 md:mt-0">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('is_active', true) ? 'checked' : '' }}>
            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Aktifkan Hero Ini</label>
        </div>
    </div>

    <!-- Buttons -->
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
@endsection
