@extends('template2.layouts.template2')

@section('content')
    @php
        // Ambil subdomain saat ini sekali saja dari parameter rute agar lebih efisien.
        $currentSubdomain = request()->route('subdomain');
    @endphp
    <section id="home-section" class="hero">
        <div class="home-slider owl-carousel">
            {{-- Lakukan perulangan untuk setiap item hero yang aktif --}}
            @forelse ($heroes as $hero)
                <div class="slider-item" style="background-image: url('{{ asset('storage/' . $hero->image) }}');">
                    <div class="overlay"></div>
                    <div class="container">
                        <div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">

                            <div class="col-md-12 ftco-animate text-center">
                                <h1 class="mb-2">{{ $hero->title }}</h1>
                                <h2 class="subheading mb-4">{{ $hero->subtitle }}</h2>

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
                <div class="slider-item" style="background-image: url('{{ asset('images/bg_1.jpg') }}');">
                    <div class="overlay"></div>
                    <div class="container">
                        <div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">
                            <div class="col-md-12 ftco-animate text-center">
                                <h1 class="mb-2">Selamat Datang di Vegefoods</h1>
                                <h2 class="subheading mb-4">Kami menyediakan sayuran & buah organik</h2>
                                <p><a href="#" class="btn btn-primary">Lihat Detail</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse

        </div>
    </section>

    {{-- Letakkan kode ini SETELAH tag </section> dari hero slider --}}

    <section class="ftco-section ftco-no-pt ftco-no-pb py-5 bg-light">
        <div class="container">
            <div class="row no-gutters ftco-services">

                {{-- Lakukan perulangan untuk setiap item banner yang aktif --}}
                @forelse ($banners as $banner)
                    <div class="col-md-4 text-center d-flex align-self-stretch ftco-animate">
                        <div class="media block-6 services mb-md-0 mb-4">
                            {{-- Seluruh banner bisa diklik jika link_url ada --}}
                            <a href="{{ $banner->link_url ?? '#' }}" class="d-block">
                                <div class="banner-item"
                                    style="background-image: url('{{ asset('storage/' . $banner->image) }}');">
                                    <div class="banner-overlay"></div>
                                    <div class="banner-text">
                                        <h3>{{ $banner->title }}</h3>
                                        <span>{{ $banner->subtitle }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    {{-- Bagian ini akan kosong jika tidak ada banner yang aktif, sehingga tidak merusak tampilan --}}
                @endforelse

            </div>
        </div>
    </section>

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

    {{-- ... dan section lainnya ... --}}
@endsection
