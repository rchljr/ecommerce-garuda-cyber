@extends('template2.layouts.template2')

@section('content')
    <div class="hero-wrap hero-bread" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    <p class="breadcrumbs"><span class="mr-2"><a href="/">Home</a></span> <span>Produk</span></p>
                    <h1 class="mb-0 bread">Produk Kami</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 mb-5 text-center">
                    {{-- Kategori Dinamis --}}
                    <ul class="product-category">
                        <li><a href="{{ route('products.index') }}" class="{{ !request('category') ? 'active' : '' }}">All</a></li>
                        @foreach ($categories as $category)
                            <li><a href="{{ route('products.index', ['category' => $category->slug]) }}" class="{{ request('category') == $category->slug ? 'active' : '' }}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row">
                {{-- Perulangan Produk Dinamis --}}
                @forelse ($products as $product)
                    <div class="col-md-6 col-lg-3 ftco-animate">
                        <div class="product">
                            <a href="#" class="img-prod">
                                {{-- Ganti dengan path gambar dari database --}}
                                <img class="img-fluid" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @if($product->discount_percentage)
                                    <span class="status">{{ $product->discount_percentage }}%</span>
                                @endif
                                <div class="overlay"></div>
                            </a>
                            <div class="text py-3 pb-4 px-3 text-center">
                                <h3><a href="#">{{ $product->name }}</a></h3>
                                <div class="d-flex">
                                    <div class="pricing">
                                        {{-- Ganti dengan harga dari database --}}
                                        <p class="price">
                                            @if($product->discount_price)
                                                <span class="mr-2 price-dc">Rp{{ number_format($product->price) }}</span>
                                                <span class="price-sale">Rp{{ number_format($product->discount_price) }}</span>
                                            @else
                                                <span class="price">Rp{{ number_format($product->price) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="bottom-area d-flex px-3">
                                    <div class="m-auto d-flex">
                                        <a href="#" class="add-to-cart d-flex justify-content-center align-items-center text-center">
                                            <span><i class="ion-ios-cart"></i></span>
                                        </a>
                                        {{-- ... tombol lainnya ... --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>Produk tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
            <div class="row mt-5">
                <div class="col text-center">
                    {{-- Pagination Dinamis --}}
                    <div class="block-27">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection