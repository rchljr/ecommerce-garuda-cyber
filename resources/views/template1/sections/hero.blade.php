{{-- resources/views/template1/sections/hero.blade.php --}}
{{-- Variabel $content berisi array data hero section dari CMS, sekarang dengan 'slides' array --}}

<section class="hero">
    <div class="hero__slider owl-carousel">
        @php
            $slides = $content['slides'] ?? [];
        @endphp

        @forelse ($slides as $slide)
            <div class="hero__items set-bg" data-setbg="{{ asset('storage/' . ($slide['background_image'] ?? 'template1/img/hero/hero-1.jpg')) }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                {{-- Subtitle dinamis dari CMS --}}
                                <h6>{{ $slide['subtitle'] ?? 'Subtitle Default' }}</h6>
                                {{-- Judul dinamis dari CMS --}}
                                <h2>{{ $slide['title'] ?? 'Judul Hero Default' }}</h2>
                                {{-- Deskripsi dinamis dari CMS --}}
                                <p>{{ $slide['description'] ?? 'Deskripsi default untuk Hero Section ini. Anda bisa mengeditnya di panel admin.' }}</p>
                                
                                {{-- Tombol dinamis dari CMS (tampil hanya jika teks tombol ada) --}}
                                @if (isset($slide['button_text']) && $slide['button_text'])
                                    <a href="{{ $slide['button_url'] ?? '#' }}" class="primary-btn">{{ $slide['button_text'] }} <span class="arrow_right"></span></a>
                                @endif

                                {{-- Ikon Sosial Media (tetap statis di sini, bisa dibuat dinamis dari CMS jika diinginkan) --}}
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
            {{-- Fallback jika tidak ada slide yang dikonfigurasi di CMS --}}
            <div class="hero__items set-bg" data-setbg="{{ asset('template1/img/hero/hero-1.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Default Collection (No Slides Configured)</h6>
                                <h2>Default Fall - Winter Collections 2030</h2>
                                <p>A specialist label creating luxury essentials. Ethically crafted with an unwavering
                                commitment to exceptional quality.</p>
                                <a href="#" class="primary-btn">Shop now <span class="arrow_right"></span></a>
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
