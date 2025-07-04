@extends('layouts.mitra')
@section('title', 'Pengaturan Tema Toko')

@section('content')
<div class="bg-gray-100 min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="container mx-auto max-w-4xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Pengaturan Tema Toko</h1>
            <p class="text-gray-500 mt-1">Sesuaikan tampilan toko online Anda di sini.</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Sukses</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-md">
            <form action="{{ route('mitra.tema.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    {{-- TAMBAHKAN INPUT TERSEMBUNYI UNTUK SUBDOMAIN ID --}}
                    {{-- Asumsi Anda mengirimkan variabel $subdomain dari controller --}}
                    {{-- Jika tidak, Anda bisa mengambilnya dari Auth::user()->subdomain->id --}}
                    @if(Auth::user()->subdomain)
                        <input type="hidden" name="subdomain_id" value="{{ Auth::user()->subdomain->id }}">
                    @endif

                    <!-- Pilihan Template -->
                    <div>
                        <label for="template_name" class="block text-sm font-medium text-gray-700">Pilih Template</label>
                        <select id="template_name" name="template_name" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($templates as $value => $label)
                                <option value="{{ $value }}" {{ old('template_name', $tema->template_name) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('template_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nama Toko -->
                    <div>
                        <label for="shop_name" class="block text-sm font-medium text-gray-700">Nama Toko</label>
                        <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name', $tema->shop_name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('shop_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Deskripsi Toko -->
                    <div>
                        <label for="shop_description" class="block text-sm font-medium text-gray-700">Deskripsi Singkat Toko</label>
                        <textarea id="shop_description" name="shop_description" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('shop_description', $tema->shop_description) }}</textarea>
                        @error('shop_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Logo Toko</label>
                        <div class="mt-1 flex items-center">
                            @if($tema->shop_logo)
                                <img src="{{ asset('storage/' . $tema->shop_logo) }}" alt="Logo Saat Ini" class="h-16 w-16 rounded-full object-cover">
                            @else
                                <span class="inline-block h-16 w-16 rounded-full overflow-hidden bg-gray-100">
                                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </span>
                            @endif
                            <input type="file" name="shop_logo" class="ml-5">
                        </div>
                        @error('shop_logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Pilihan Warna -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="primary_color" class="block text-sm font-medium text-gray-700">Warna Primer</label>
                            <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $tema->primary_color ?? '#4F46E5') }}" class="mt-1 h-10 w-full block border border-gray-300 rounded-md">
                            @error('primary_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="secondary_color" class="block text-sm font-medium text-gray-700">Warna Sekunder</label>
                            <input type="color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $tema->secondary_color ?? '#D946EF') }}" class="mt-1 h-10 w-full block border border-gray-300 rounded-md">
                            @error('secondary_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
