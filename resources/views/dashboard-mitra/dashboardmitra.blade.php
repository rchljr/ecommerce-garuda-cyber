@extends('layouts.mitra')

@section('title', 'Mitra Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang di Mitra Dashboard!</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Manajemen Halaman</h2>
                <p class="text-gray-600 mb-4">Kelola semua halaman website Anda dan struktur kontennya.</p>
                <a href="{{ route('mitra.pages.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Kelola Halaman
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Manajemen Produk</h2>
                <p class="text-gray-600 mb-4">Tambahkan, edit, atau hapus produk di toko Anda.</p>
                <a href="{{ route('mitra.products.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Kelola Produk
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Pengaturan Hero Section (Lama)</h2>
                <p class="text-gray-600 mb-4">Kelola Hero Section Anda jika menggunakan modul terpisah.</p>
                <a href="{{ route('mitra.hero_sections.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Kelola Hero Sections
                </a>
            </div>
            {{-- Tambahkan card untuk modul lain seperti Blog, Pesanan, dll. --}}
        </div>
    </div>
@endsection
