@extends('template1.layouts.template1')

{{-- @section('title', 'Beranda - Male-Fashion') --}}

@section('content')
    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="hero__slider owl-carousel">
            @forelse ($heroes as $hero)
            <div class="hero__items set-bg" data-setbg="{{ $hero->image_url }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                @if ($hero->subtitle)
                                    <h6>{{ $hero->subtitle }}</h6>
                                @endif
                                <h2>{{ $hero->title }}</h2>
                                @if ($hero->description)
                                    <p>{{ $hero->description }}</p>
                                @endif
                                @if ($hero->button_text && $hero->button_url)
                                    <a href="{{ $hero->button_url }}" class="primary-btn">{{ $hero->button_text }} <span class="arrow_right"></span></a>
                                @endif
                                <div class="hero__social">
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
            {{-- Konten default jika tidak ada hero yang diatur --}}
            <div class="hero__items set-bg" data-setbg="{{ asset('template1/img/hero/hero-default.jpg') }}"> {{-- Pastikan ada gambar default di public/template1/img/hero/hero-default.jpg --}}
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Selamat Datang!</h6>
                                <h2>Toko Fashion Terbaik Anda</h2>
                                <p>Ini Adalah Hero Bisa Kamu Edit diDasboard</p>
                                <a href="#" class="primary-btn">Telusuri Sekarang <span class="arrow_right"></span></a>
                                <div class="hero__social">
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
            @endforelse
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                @forelse ($banners as $banner)
                    <div class="{{ $loop->first && $banners->count() > 1 ? 'col-lg-7 offset-lg-4' : ($loop->index === 1 && $banners->count() > 2 ? 'col-lg-5' : 'col-lg-7') }}">
                        <div class="banner__item {{ $loop->index === 1 ? 'banner__item--middle' : ($loop->last && $banners->count() > 1 ? 'banner__item--last' : '') }}">
                            <div class="banner__item__pic">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                            </div>
                            <div class="banner__item__text">
                                @if ($banner->title)
                                    <h2>{{ $banner->title }}</h2>
                                @endif
                                @if ($banner->link_url)
                                    <a href="{{ $banner->link_url }}">{{ $banner->button_text ?? 'Shop now' }}</a>
                                @else
                                    <p>{{ $banner->button_text ?? '' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Konten default jika tidak ada banner yang diatur --}}
                    <div class="col-lg-7 offset-lg-4">
                        <div class="banner__item">
                            <div class="banner__item__pic">
                                <img src="{{ asset('template1/img/banner/banner-1.jpg') }}" alt="Default Banner 1">
                            </div>
                            <div class="banner__item__text">
                                <h2>Banner1</h2>
                                <a href="#">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="banner__item banner__item--middle">
                            <div class="banner__item__pic">
                                <img src="{{ asset('template1/img/banner/banner-2.jpg') }}" alt="Default Banner 2">
                            </div>
                            <div class="banner__item__text">
                                <h2>Banner2</h2>
                                <a href="#">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="banner__item banner__item--last">
                            <div class="banner__item__pic">
                                <img src="{{ asset('template1/img/banner/banner-3.jpg') }}" alt="Default Banner 3">
                            </div>
                            <div class="banner__item__text">
                                <h2>Banner3</h2>
                                <a href="#">Shop now</a>
                            </div>
                        </div>
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
                $allProducts = collect($bestSellers ?? [])->merge($newArrivals ?? [])->merge($hotSales ?? [])->unique('id');
            @endphp

            <div class="row product__filter">
                @forelse ($allProducts as $product)
                    @php
                        // Menentukan class untuk filter MixItUp
                        $classes = '';
                        if (($bestSellers ?? collect())->contains($product)) $classes .= ' best-seller';
                        if (($newArrivals ?? collect())->contains($product)) $classes .= ' new-arrival';
                        if (($hotSales ?? collect())->contains($product)) $classes .= ' hot-sale';
                    @endphp
                    <div class="col-lg-3 col-md-6 col-sm-6 mix{{ $classes }}">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                {{-- PERBAIKAN: Menggunakan flag dari database --}}
                                @if($product->is_new_arrival)
                                    <span class="label">New</span>
                                @elseif($product->is_hot_sale)
                                    <span class="label">Sale</span>
                                @endif
                                <ul class="product__hover">
                                    <li><a href="#"><img src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a></li>
                                    <li><a href="{{ route('shop.details', $product->slug) }}"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a></li>
                                </ul>
                            </div>
                            <div class="product__item__text">
                                <h6>{{ $product->name }}</h6>
                                <a href="#" class="add-cart">+ Add To Cart</a>
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

    <!-- Categories Section Begin -->
    {{-- <section class="categories spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="categories__text">
                        <h2>Clothings Hot <br /> <span>Shoe Collection</span> <br /> Accessories</h2>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="categories__hot__deal">
                        <img src="{{ asset('template1/img/product-sale.png') }}" alt="">
                        <div class="hot__deal__sticker">
                            <span>Sale Of</span>
                            <h5>$29.99</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 offset-lg-1">
                    <div class="categories__deal__countdown">
                        <span>Deal Of The Week</span>
                        <h2>Multi-pocket Chest Bag Black</h2>
                        <div class="categories__deal__countdown__timer" id="countdown">
                            <div class="cd-item">
                                <span>3</span>
                                <p>Days</p>
                            </div>
                            <div class="cd-item">
                                <span>1</span>
                                <p>Hours</p>
                            </div>
                            <div class="cd-item">
                                <span>50</span>
                                <p>Minutes</p>
                            </div>
                            <div class="cd-item">
                                <span>18</span>
                                <p>Seconds</p>
                            </div>
                        </div>
                        <a href="#" class="primary-btn">Shop now</a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Categories Section End -->

    <!-- Instagram Section Begin -->
    {{-- <section class="instagram spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="instagram__pic">
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('template1/img/instagram/instagram-1.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('template1/img/instagram/instagram-2.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('template1/img/instagram/instagram-3.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('template1/img/instagram/instagram-4.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('template1/img/instagram/instagram-5.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('template1/img/instagram/instagram-6.jpg') }}"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="instagram__text">
                        <h2>Instagram</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                        labore et dolore magna aliqua.</p>
                        <h3>#Male_Fashion</h3>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Instagram Section End -->

    <!-- Latest Blog Section Begin -->
    {{-- <section class="latest spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Latest News</span>
                        <h2>Fashion New Trends</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic set-bg" data-setbg="{{ asset('template1/img/blog/blog-1.jpg') }}"></div>
                        <div class="blog__item__text">
                            <span><img src="{{ asset('template1/img/icon/calendar.png') }}" alt=""> 16 February 2020</span>
                            <h5>What Curling Irons Are The Best Ones</h5>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic set-bg" data-setbg="{{ asset('template1/img/blog/blog-2.jpg') }}"></div>
                        <div class="blog__item__text">
                            <span><img src="{{ asset('template1/img/icon/calendar.png') }}" alt=""> 21 February 2020</span>
                            <h5>Eternity Bands Do Last Forever</h5>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic set-bg" data-setbg="{{ asset('template1/img/blog/blog-3.jpg') }}"></div>
                        <div class="blog__item__text">
                            <span><img src="{{ asset('template1/img/icon/calendar.png') }}" alt=""> 28 February 2020</span>
                            <h5>The Health Benefits Of Sunglasses</h5>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Latest Blog Section End -->
@endsection