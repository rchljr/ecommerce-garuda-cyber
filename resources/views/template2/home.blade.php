@extends('template2.layouts.template2')

@section('content')
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
                                    <p><a href="{{ $hero->button_url }}" class="btn btn-primary">{{ $hero->button_text }}</a></p>
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
    
    {{-- ... dan section lainnya ... --}}
@endsection