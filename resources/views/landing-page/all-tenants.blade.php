@extends('layouts.tenants')
@section('title', 'Jelajahi Toko Mitra')

@push('styles')
{{-- Tambahkan style jika diperlukan, misalnya untuk animasi kustom --}}
<style>
    .voucher-strip {
        border-style: dashed;
    }
    .group:hover .kunjungi-badge {
        transform: translateY(-4px);
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
    }
</style>
@endpush

@section('content')
    <div class="bg-gray-50">
        <main>
            <!-- Header dengan Latar Belakang Gradien -->
            <div class="mb-8 bg-gradient-to-br from-gray-800 to-gray-900">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                        Temukan Mitra Terbaik Kami
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-300">
                        Jelajahi beragam toko dari para mitra kreatif kami. Manfaatkan penawaran dan voucher eksklusif yang tersedia.
                    </p>
                </div>
            </div>

            <!-- Konten Utama: Filter dan Daftar Toko -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-[-4rem] pb-20">
                {{-- Filter dan Pencarian dengan Efek Shadow yang Lebih Baik --}}
                <div class="bg-white rounded-lg shadow-xl p-4 sm:p-6 mb-8 sticky top-20 z-20">
                    {{-- Form Pencarian --}}
                    <form action="{{ route('tenants.index') }}" method="GET">
                        <label for="search" class="sr-only">Cari Nama Toko</label>
                        <div class="relative">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200"
                                placeholder="Cari berdasarkan nama toko...">
                        </div>
                    </form>

                    {{-- Filter Kategori dalam bentuk Pills --}}
                    <div class="mt-5">
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="text-sm font-medium text-gray-700 mr-2">Kategori:</span>
                            <a href="{{ route('tenants.index', request()->except('category')) }}"
                                class="px-4 py-1.5 text-sm font-medium rounded-full transition-all duration-200 ease-in-out
                                        {{ !request('category') ? 'bg-red-600 text-white shadow-md scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                Semua
                            </a>
                            @foreach ($categories as $category)
                                <a href="{{ route('tenants.index', array_merge(request()->query(), ['category' => $category->slug, 'page' => 1])) }}"
                                    class="px-4 py-1.5 text-sm font-medium rounded-full transition-all duration-200 ease-in-out
                                            {{ request('category') == $category->slug ? 'bg-red-600 text-white shadow-md scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Daftar Toko Mitra --}}
                @if($partners->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        @foreach($partners as $partner)
                            @php
                                $shop = $partner->shop;
                                $subdomain = $partner->subdomain;
                                $vouchers = $partner->activeVouchers; // Mengambil voucher yang sudah di-load
                            @endphp
                            @if($shop && $subdomain)
                                {{-- KARTU MITRA YANG DI-RENOVASI --}}
                                <div class="group bg-white rounded-xl shadow-md transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col">
                                    <a href="{{ route('tenant.home', ['subdomain' => $subdomain->subdomain_name]) }}" class="flex flex-col h-full">
                                        <div class="relative">
                                            {{-- Banner --}}
                                            <div class="h-40 bg-gray-200 rounded-t-xl overflow-hidden">
                                                <img src="{{ asset('storage/' . $shop->shop_banner) }}"
                                                    onerror="this.onerror=null;this.src='https://placehold.co/600x400/f1f5f9/cbd5e1?text={{ urlencode($shop->shop_name) }}';"
                                                    alt="Banner {{ $shop->shop_name }}"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                            </div>
                                            {{-- Logo --}}
                                            <div class="absolute bottom-0 left-5 transform translate-y-1/2">
                                                <img src="{{ asset('storage/' . $shop->shop_logo) }}"
                                                    onerror="this.onerror=null;this.src='https://placehold.co/80x80/ef4444/ffffff?text={{ substr($shop->shop_name, 0, 1) }}';"
                                                    alt="Logo {{ $shop->shop_name }}"
                                                    class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 group-hover:rotate-6">
                                            </div>
                                        </div>

                                        {{-- Konten Teks --}}
                                        <div class="p-5 pt-12 flex flex-col flex-grow">
                                            <h3 class="font-bold text-lg text-gray-900 truncate" title="{{ $shop->shop_name }}">{{ $shop->shop_name }}</h3>
                                            <p class="text-sm text-gray-500 mt-1 h-10 overflow-hidden">
                                                {{ $shop->shop_tagline ?: 'Menyediakan produk terbaik untuk Anda.' }}
                                            </p>

                                            {{-- BAGIAN VOUCHER --}}
                                            @if($vouchers->isNotEmpty())
                                            <div class="mt-4 space-y-2">
                                                {{-- Ambil 2 voucher terbaik --}}
                                                @foreach($vouchers->take(2) as $voucher)
                                                <div class="voucher-strip border-red-400 border-2 rounded-lg p-2 flex items-center gap-2 bg-red-50">
                                                    <svg class="w-6 h-6 text-red-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                                    </svg>
                                                    <div class="text-xs">
                                                        @if ($voucher->discount < 100)
                                                            <p class="font-semibold text-red-700">Diskon {{ (int)$voucher->discount }}%</p>
                                                        @else
                                                            <p class="font-semibold text-red-700">Diskon Rp {{ number_format($voucher->discount, 0, ',', '.') }}</p>
                                                        @endif
                                                        <p class="text-red-600">Min. belanja Rp {{ number_format($voucher->min_spending, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @else
                                            {{-- Beri ruang kosong agar layout tetap konsisten --}}
                                            <div class="mt-4 h-16"></div>
                                            @endif

                                            {{-- Tombol Aksi --}}
                                            <div class="mt-auto pt-4 text-center">
                                                <span class="kunjungi-badge inline-block bg-red-600 text-white text-sm font-bold px-6 py-2 rounded-full group-hover:bg-red-700 transition-all duration-300 ease-in-out">
                                                    Kunjungi Toko
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-12">
                        {{ $partners->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-lg shadow-md">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Mitra Ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau filter Anda.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection
