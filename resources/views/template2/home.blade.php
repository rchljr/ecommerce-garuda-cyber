{{-- resources/views/template2/home.blade.php --}}
@extends('template2.layouts.template')

@section('content')
    <section id="home-section" class="hero">
        <div class="home-slider owl-carousel">
            {{-- Lakukan perulangan untuk setiap item hero yang aktif --}}
            @forelse ($heroes as $hero)
                {{-- PERUBAHAN: Tambahkan ID unik untuk setiap slide --}}
                <div class="slider-item" id="preview-hero-bg-{{ $hero->id }}"
                    style="background-image: url('{{ $hero->image_url }}');">
                    <div class="overlay"></div>
                    <div class="container">
                        <div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">

                            <div class="col-md-12 ftco-animate text-center">
                                {{-- PERUBAHAN: Tambahkan ID unik untuk judul dan subtitle --}}
                                <h1 class="mb-2" id="preview-hero-title-{{ $hero->id }}">{{ $hero->title }}</h1>
                                <h2 class="subheading mb-4" id="preview-hero-subtitle-{{ $hero->id }}">
                                    {{ $hero->subtitle }}</h2>

                                {{-- Tampilkan tombol hanya jika teks dan URL-nya ada --}}
                                @if ($hero->button_text && $hero->button_url)
                                    <p><a href="{{ $hero->button_url }}"
                                            class="btn btn-primary">{{ $hero->button_text }}</a></p>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                {{-- Tampilan fallback jika tidak ada data hero yang aktif di database --}}
                <div class="slider-item" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
                    <div class="overlay"></div>
                    <div class="container">
                        <div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">
                            <div class="col-md-12 ftco-animate text-center">
                                <h1 class="mb-2">Selamat Datang di Toko Anda</h1>
                                <h2 class="subheading mb-4">Atur slide hero Anda di editor</h2>
                                <p><a href="#" class="btn btn-primary">Lihat Detail</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse

        </div>
    </section>

    <section class="ftco-section ftco-no-pt ftco-no-pb py-5 bg-light">
        <div class="container">
            <div class="row no-gutters ftco-services">

                {{-- Lakukan perulangan untuk setiap item banner yang aktif --}}
                @forelse ($banners as $banner)
                    <div class="col-md-4 text-center d-flex align-self-stretch ftco-animate">
                        <div class="media block-6 services mb-md-0 mb-4">
                            <a href="{{ $banner->link_url ?? '#' }}" class="d-block">
                                {{-- PERUBAHAN: Tambahkan ID unik untuk setiap banner --}}
                                <div class="banner-item" id="preview-banner-bg-{{ $banner->id }}"
                                    style="background-image: url('{{ $banner->image_url }}');">
                                    <div class="banner-overlay"></div>
                                    <div class="banner-text">
                                        <h3 id="preview-banner-title-{{ $banner->id }}">{{ $banner->title }}</h3>
                                        <span
                                            id="preview-banner-subtitle-{{ $banner->id }}">{{ $banner->subtitle }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    {{-- Bagian ini akan kosong jika tidak ada banner --}}
                @endforelse

            </div>
        </div>
    </section>

    <!-- =================================== -->
    <!--          BANNER SECTION BARU        -->
    <!-- =================================== -->
    {{-- <section class="ftco-section ftco-category ftco-no-pt">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6 order-md-last align-items-stretch d-flex">
                            <div class="category-wrap-2 ftco-animate img align-self-stretch d-flex" style="background-image: url(images/category.jpg);">
                                <div class="text text-center">
                                    <h2>Sayuran</h2>
                                    <p>Lindungi kesehatan Anda</p>
                                    <p><a href="#" class="btn btn-primary">Belanja Sekarang</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Banner 1 --}}
                            {{-- <div class="category-wrap ftco-animate img mb-4 d-flex align-items-end" style="background-image: url(images/category-1.jpg);">
                                <div class="text px-3 py-1">
                                    <h2 class="mb-0"><a href="#">Buah-buahan</a></h2>
                                </div>
                            </div> --}}
                            {{-- Banner 2 --}}
                            {{-- <div class="category-wrap ftco-animate img d-flex align-items-end" style="background-image: url(images/category-2.jpg);">
                                <div class="text px-3 py-1">
                                    <h2 class="mb-0"><a href="#">Sayuran Kering</a></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4"> --}}
                    {{-- Banner 3 --}}
                    {{-- <div class="category-wrap ftco-animate img mb-4 d-flex align-items-end" style="background-image: url(images/category-3.jpg);">
                        <div class="text px-3 py-1">
                            <h2 class="mb-0"><a href="#">Jus</a></h2>
                        </div>
                    </div> --}}
                    {{-- Banner 4 --}}
                    {{-- <div class="category-wrap ftco-animate img d-flex align-items-end" style="background-image: url(images/category-4.jpg);">
                        <div class="text px-3 py-1">
                            <h2 class="mb-0"><a href="#">Rempah</a></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>  --}}
    <!--       AKHIR BANNER SECTION BARU       -->


    {{-- ... dan section lainnya ... --}}
@endsection

{{-- Tambahkan sedikit CSS ini di dalam tag <head> layout utama Anda atau di file CSS --}}
@push('styles')
    <style>
        .banner-item {
            height: 250px;
            width: 100%;
            position: relative;
            background-size: cover;
            background-position: center center;
            border-radius: 5px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .banner-item:hover {
            transform: scale(1.05);
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            /* Overlay gelap agar teks terbaca */
            z-index: 1;
        }

        .banner-text {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: #fff;
            z-index: 2;
        }

        .banner-text h3 {
            color: #fff;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 5px;
        }

        .banner-text span {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
@endpush
