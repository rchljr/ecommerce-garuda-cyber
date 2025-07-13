@extends('template2.layouts.template2')

@section('content')
    @php
        // Persiapan variabel untuk tenant dan mode preview
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    <div class="hero-wrap hero-bread" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    {{-- PERBAIKAN: Breadcrumbs dinamis dan tenant-aware --}}
                    <p class="breadcrumbs">
                        <span class="mr-2"><a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a></span>
                        <span class="mr-2"><a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Produk</a></span>
                        <span>Detail Produk</span>
                    </p>
                    <h1 class="mb-0 bread">{{ $product->name }}</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5 ftco-animate">
                    {{-- PERBAIKAN: Gambar produk dinamis --}}
                    <a href="{{ asset('storage/' . $product->main_image) }}" class="image-popup">
                        <img src="{{ asset('storage/' . $product->main_image) }}" class="img-fluid"
                            alt="{{ $product->name }}">
                    </a>
                </div>
                <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                    {{-- PERBAIKAN: Nama produk dinamis --}}
                    <h3>{{ $product->name }}</h3>

                    {{-- PERBAIKAN: Harga produk dinamis --}}
                    <p class="price">
                        @if (isset($product->discount_price))
                            <span class="mr-2 price-dc">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="price-sale">Rp{{ number_format($product->discount_price, 0, ',', '.') }}</span>
                        @else
                            <span>Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                    </p>

                    {{-- PERBAIKAN: Deskripsi produk dinamis --}}
                    <p>{{ $product->description ?? 'Deskripsi untuk produk ini belum tersedia. Hubungi kami untuk informasi lebih lanjut.' }}
                    </p>

                    {{-- PERBAIKAN: Form untuk menambahkan ke keranjang --}}
                    <form action="#" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="row mt-4">
                            <div class="col-md-6">
                                {{-- PERBAIKAN: Varian produk dinamis dari database --}}
                                <div class="form-group d-flex">
                                    <div class="select-wrap">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="variant_id" id="variant_select" class="form-control">
                                            @forelse ($product->variants as $variant)
                                                <option value="{{ $variant->id }}" data-stock="{{ $variant->stock }}">
                                                    {{ $variant->size ?? ($variant->color ?? 'Standar') }}
                                                </option>
                                            @empty
                                                <option value="">Varian tidak tersedia</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100"></div>
                            <div class="input-group col-md-6 d-flex mb-3">
                                <span class="input-group-btn mr-2">
                                    <button type="button" class="quantity-left-minus btn" data-type="minus" data-field="">
                                        <i class="ion-ios-remove"></i>
                                    </button>
                                </span>
                                <input type="text" id="quantity" name="quantity" class="form-control input-number"
                                    value="1" min="1" max="100">
                                <span class="input-group-btn ml-2">
                                    <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="">
                                        <i class="ion-ios-add"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="w-100"></div>
                            {{-- PERBAIKAN: Info stok dinamis --}}
                            <div class="col-md-12">
                                <p id="stock_info" style="color: #000;">Stok tersedia:
                                    {{ $product->variants->first()->stock ?? 0 }}</p>
                            </div>
                        </div>
                        <p><button type="submit" class="btn btn-black py-3 px-5">Tambah ke Keranjang</button></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- PERBAIKAN: Bagian produk terkait dibuat dinamis --}}
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center mb-3 pb-3">
                <div class="col-md-12 heading-section text-center ftco-animate">
                    <span class="subheading">Produk</span>
                    <h2 class="mb-4">Produk Terkait</h2>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                @forelse ($relatedProducts as $related)
                    <div class="col-md-6 col-lg-3 ftco-animate">
                        <div class="product">
                            <a href="{{ !$isPreview ? route('tenant.shop.detail', ['subdomain' => $currentSubdomain, 'product' => $related->slug]) : '#' }}"
                                class="img-prod">
                                <img class="img-fluid" src="{{ asset('storage/' . $related->main_image) }}"
                                    alt="{{ $related->name }}">
                                <div class="overlay"></div>
                            </a>
                            <div class="text py-3 pb-4 px-3 text-center">
                                <h3><a
                                        href="{{ !$isPreview ? route('tenant.shop.detail', ['subdomain' => $currentSubdomain, 'product' => $related->slug]) : '#' }}">{{ $related->name }}</a>
                                </h3>
                                <div class="d-flex">
                                    <div class="pricing">
                                        <p class="price"><span>Rp{{ number_format($related->price, 0, ',', '.') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>Tidak ada produk terkait.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Script untuk update info stok saat varian diganti --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const variantSelect = document.getElementById('variant_select');
                const stockInfo = document.getElementById('stock_info');

                if (variantSelect) {
                    variantSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const stock = selectedOption.getAttribute('data-stock');
                        stockInfo.textContent = 'Stok tersedia: ' + stock;
                    });
                }
            });
        </script>
    @endpush
@endsection
