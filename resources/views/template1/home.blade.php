{{-- resources/views/template1/home.blade.php --}}
@extends('template1.layouts.template')

@section('content')
    @php
        $isPreview = $isPreview ?? false;
    @endphp

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="hero__slider owl-carousel">
            {{-- Menggunakan loop untuk menampilkan semua hero yang aktif dari mitra --}}
            @forelse ($heroes as $hero)
                <div class="hero__items set-bg" data-setbg="{{ $hero->image_url }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    {{-- Data diambil dari objek $hero --}}
                                    <h6 id="preview-hero-subtitle">{{ $hero->subtitle }}</h6>
                                    <h2 id="preview-hero-title">{{ $hero->title }}</h2>
                                    <p id="preview-hero-description">{{ $hero->description }}</p>

                                    {{-- Tombol ini bisa diatur warnanya dari shop_settings jika mau --}}
                                    <a href="{{ $hero->button_url }}" class="primary-btn preview-button">
                                        <span id="preview-hero-button-text">{{ $hero->button_text }}</span>
                                        <span class="arrow_right"></span>
                                    </a>

                                    <div class="hero__social">
                                        {{-- Link sosial media bisa diambil dari $settings jika masih digunakan --}}
                                        <a href="#"><i class="fa fa-facebook"></i></a>
                                        <a href="#"><i class="fa fa-twitter"></i></a>
                                        <a href="#"><i class="fa fa-pinterest"></i></a>
                                        <a href="#"><i class="fa fa-instagram"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Konten default jika tidak ada hero yang diatur oleh mitra --}}
                <div class="hero__items set-bg" data-setbg="{{ asset('template1/img/hero/hero-default.jpg') }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    <h6>Selamat Datang!</h6>
                                    <h2>Toko Fashion Terbaik Anda</h2>
                                    <p>Atur tampilan hero section Anda melalui dashboard editor.</p>
                                    <a href="#" class="primary-btn">Telusuri Sekarang <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                {{-- Menggunakan loop untuk menampilkan banner dari mitra --}}
                @forelse ($banners as $banner)
                    {{-- Anda perlu menyesuaikan class col-lg-* berdasarkan jumlah banner --}}
                    <div class="col-lg-4">
                        <div class="banner__item">
                            <div class="banner__item__pic">
                                <img id="preview-banner-{{ $loop->index }}-img" src="{{ $banner->image_url }}"
                                    alt="{{ $banner->title }}">
                            </div>
                            <div class="banner__item__text">
                                <h2 id="preview-banner-{{ $loop->index }}-title">{{ $banner->title }}</h2>
                                <a href="{{ $banner->link_url }}">Shop now</a>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Konten default jika tidak ada banner --}}
                    <div class="col-lg-12 text-center">
                        <p>Banner belum diatur. Silakan tambahkan melalui dashboard.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        {{-- Filter ini akan bekerja dengan class pada produk di bawah --}}
                        <li class="active" data-filter=".best-seller">Best Sellers</li>
                        <li data-filter=".new-arrival">New Arrivals</li>
                        <li data-filter=".hot-sale">Hot Sales</li>
                    </ul>
                </div>
            </div>

            {{-- Mengganti grid produk statis menjadi dinamis --}}
            @php
                // Menggabungkan semua produk dan memastikan tidak ada duplikat
                $allProducts = collect($bestSellers ?? [])
                    ->merge($newArrivals ?? [])
                    ->merge($hotSales ?? [])
                    ->unique('id');
            @endphp

            <div class="row product__filter">
                @forelse ($allProducts as $product)
                    @php
                        // Menentukan class untuk filter MixItUp
                        $classes = '';
                        if (($bestSellers ?? collect())->contains($product)) {
                            $classes .= ' best-seller';
                        }
                        if (($newArrivals ?? collect())->contains($product)) {
                            $classes .= ' new-arrival';
                        }
                        if (($hotSales ?? collect())->contains($product)) {
                            $classes .= ' hot-sale';
                        }
                    @endphp
                    <div class="col-lg-3 col-md-6 col-sm-6 mix{{ $classes }}">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                {{-- PERBAIKAN: Menggunakan flag dari database --}}
                                @if ($product->is_new_arrival)
                                    <span class="label">New</span>
                                @elseif($product->is_hot_sale)
                                    <span class="label">Sale</span>
                                @endif
                                <ul class="product__hover">
                                    <li><a href="#"><img src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a></li>
                                    {{-- Tambahkan parameter subdomain ke rute tenant.product.details --}}
                                    <li><a
                                            href="{{  !$isPreview ? route('tenant.product.details', ['subdomain' => $subdomainName, 'product' => $product->slug]) : '#' }}"><img
                                                src="{{ asset('template1/img/icon/search.png') }}" alt=""></a></li>
                                </ul>
                            </div>
                            <div class="product__item__text">
                                <h6>{{ $product->name }}</h6>
                                {{-- Tambahkan parameter subdomain ke rute tenant.cart.add --}}
                                <a href="#" class="add-cart add-cart-button" data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}"
                                    data-product-image="{{ asset('storage/' . $product->main_image) }}"
                                    data-product-variants="{{ json_encode($product->variants) }}">
                                    + Add To Cart
                                </a>
                                <div class="rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa {{ ($product->rating_product ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </div>
                                <h5>Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center py-5">Belum ada produk unggulan saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Product Section End -->
@endsection