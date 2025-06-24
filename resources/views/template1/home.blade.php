@extends('template1.layouts.app')

@section('title', $page->title ?? 'Home Page')

@section('content')

    @if ($page && $page->sections->count() > 0)
        {{-- Loop melalui setiap seksi yang aktif untuk halaman ini --}}
        @foreach ($page->sections->where('is_active', true)->sortBy('order') as $section) {{-- Tambah sortBy('order') biar urut --}}
            @include('template1.sections.' . $section->section_type, ['content' => $section->content])
        @endforeach
    @else
        {{-- Konten default jika halaman 'home' atau seksinya tidak ditemukan --}}
        <section class="hero">
            <div class="hero__items set-bg" data-setbg="{{ asset('template1/img/hero/hero-1.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Default Summer Collection</h6>
                                <h2>Default Fall - Winter Collections 2030</h2>
                                <p>Default text: A specialist label creating luxury essentials. Ethically crafted with an unwavering commitment to exceptional quality.</p>
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
        </section>
        {{-- ... tambahkan default sections lainnya jika diperlukan ... --}}
    @endif

@endsection