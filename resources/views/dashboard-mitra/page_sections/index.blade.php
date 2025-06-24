@extends('layouts.mitra')

@section('title', 'Seksi untuk ' . $page->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Seksi untuk Halaman "{{ $page->name }}"</h1>
        <p class="text-gray-600 mb-4">Kelola blok konten untuk halaman ini.</p>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
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

        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('mitra.pages.sections.create', $page) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                Tambah Seksi Baru
            </a>
            <a href="{{ route('mitra.pages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Kembali ke Daftar Halaman
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Urutan
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tipe Seksi
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aktif
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Pratinjau Konten
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pageSections as $section)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $section->order }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ Str::title(str_replace('_', ' ', $section->section_type)) }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $section->is_active ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{-- Simple preview of content based on section_type --}}
                                @if($section->section_type == 'hero')
                                    @if(isset($section->content['title']))
                                        <p class="text-gray-900 text-sm font-semibold">Judul: {{ $section->content['title'] }}</p>
                                    @endif
                                    @if(isset($section->content['subtitle']))
                                        <p class="text-gray-700 text-xs">Subt.: {{ $section->content['subtitle'] }}</p>
                                    @endif
                                    @if(isset($section->content['background_image']))
                                        <img src="{{ asset('storage/' . $section->content['background_image']) }}" alt="Image" class="w-16 h-10 object-cover rounded mt-1">
                                    @endif
                                @elseif($section->section_type == 'banner')
                                    @if(isset($section->content['title_1']))
                                        <p class="text-gray-900 text-sm font-semibold">Banner 1: {{ $section->content['title_1'] }}</p>
                                    @endif
                                    @if(isset($section->content['image_1']))
                                        <img src="{{ asset('storage/' . $section->content['image_1']) }}" alt="Image" class="w-16 h-10 object-cover rounded mt-1">
                                    @endif
                                @elseif($section->section_type == 'text_block')
                                    @if(isset($section->content['heading']))
                                        <p class="text-gray-900 text-sm font-semibold">Judul Blok: {{ $section->content['heading'] }}</p>
                                    @endif
                                    <p class="text-gray-700 text-xs">{{ Str::limit(strip_tags($section->content['body'] ?? ''), 50) }}</p>
                                @else
                                    <p class="text-gray-500 text-xs">Pratinjau tidak tersedia</p>
                                @endif
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('mitra.pages.sections.edit', [$page, $section]) }}" class="inline-flex items-center px-3 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Edit
                                    </a>
                                    <form action="{{ route('mitra.pages.sections.destroy', [$page, $section]) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seksi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">Tidak ada seksi ditemukan untuk halaman ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
