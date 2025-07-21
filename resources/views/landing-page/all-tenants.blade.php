@extends('layouts.tenants')
@section('title', 'E-Commerce Garuda')

@push('styles')
    {{-- Style kustom untuk kartu dan filter --}}
    <style>
        .voucher-strip {
            border-style: dashed;
        }

        .group:hover .kunjungi-badge {
            transform: translateY(-4px);
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
        }

        .product-filter-btn {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: #4b5563;
            /* gray-600 */
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-filter-btn.active,
        .product-filter-btn:hover {
            color: #dc2626;
            /* red-600 */
            border-bottom-color: #dc2626;
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
                        Destinasi Belanja Online Pilihan Anda
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-300">
                        Temukan semua kebutuhanmu, dari fashion hingga elektronik, dengan penawaran terbaik dan voucher
                        menarik setiap hari.
                    </p>
                </div>
            </div>

            <!-- Konten Utama: Filter dan Daftar Toko -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-[-4rem] pb-20">
                {{-- Filter dan Pencarian --}}
                <div class="bg-white rounded-lg shadow-xl p-4 sm:p-6 mb-8 sticky top-20 z-20">
                    <form action="{{ route('tenants.index') }}" method="GET">
                        <label for="search" class="sr-only">Cari Produk, Toko, atau Brand</label>
                        <div class="relative">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200"
                                placeholder="Cari Toko atau Produk Pilihan Anda...">
                        </div>
                    </form>

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
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Toko Pilihan Untukmu</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($partners as $partner)
                            @include('layouts._partials.partner-card', ['partner' => $partner])
                        @endforeach
                    </div>
                    <div class="mt-12">
                        {{ $partners->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-lg shadow-md">
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Mitra Ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau filter Anda.</p>
                    </div>
                @endif

                <div class="mt-20">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Produk Pilihan Untuk Anda</h2>
                        <p class="mt-4 text-lg text-gray-600">Temukan item terbaik yang sedang tren dari berbagai toko.</p>
                    </div>

                    {{-- Product Filter Tabs --}}
                    <div class="flex justify-center mb-8">
                        <div class="filter-controls-products border-b">
                            <button data-filter="best-seller" class="product-filter-btn active">Terlaris</button>
                            <button data-filter="new-arrival" class="product-filter-btn">Terbaru</button>
                            <button data-filter="hot-sale" class="product-filter-btn">Promo</button>
                        </div>
                    </div>

                    {{-- Product Grids --}}
                    <div class="product-grid-container">
                        <div id="best-seller" class="product-grid active">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @forelse($bestSellers as $product)
                                    @include('layouts._partials.product-card', ['product' => $product])
                                @empty
                                    <p class="col-span-full text-center text-gray-500">Belum ada produk terlaris.</p>
                                @endforelse
                            </div>
                        </div>
                        <div id="new-arrival" class="product-grid hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @forelse($newArrivals as $product)
                                    @include('layouts._partials.product-card', ['product' => $product])
                                @empty
                                    <p class="col-span-full text-center text-gray-500">Belum ada produk terbaru.</p>
                                @endforelse
                            </div>
                        </div>
                        <div id="hot-sale" class="product-grid hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @forelse($hotSales as $product)
                                    @include('layouts._partials.product-card', ['product' => $product])
                                @empty
                                    <p class="col-span-full text-center text-gray-500">Belum ada produk promo.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterButtons = document.querySelectorAll('.product-filter-btn');
            const productGrids = document.querySelectorAll('.product-grid');

            if (filterButtons.length === 0) return;

            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Update button styles
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.dataset.filter;

                    // Update grid visibility
                    productGrids.forEach(grid => {
                        if (grid.id === filter) {
                            grid.classList.remove('hidden');
                            grid.classList.add('active');
                        } else {
                            grid.classList.add('hidden');
                            grid.classList.remove('active');
                        }
                    });
                });
            });
        });
    </script>
@endpush