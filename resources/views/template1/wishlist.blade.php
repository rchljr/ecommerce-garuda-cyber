@extends('template1.layouts.template')

@section('title', 'Wishlist Saya')

@section('content')
    <!-- Breadcrumb -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Wishlist Saya</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
                            <span>Wishlist</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Halaman Wishlist -->
    <section class="shopping-cart spad">
        <div class="container">
            {{-- PERBAIKAN: Tambahkan isset() untuk mencegah error jika variabel tidak ada --}}
            @if(isset($wishlistItems) && $wishlistItems->isNotEmpty())
                <div class="row">
                    <div class="col-lg-12">
                        <div class="shopping__cart__table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wishlistItems as $item)
                                        <tr>
                                            <td class="product__cart__item">
                                                <div class="product__cart__item__pic">
                                                    <img src="{{ $item->product->image_url ?? '' }}" alt="" style="width: 90px;">
                                                </div>
                                                <div class="product__cart__item__text">
                                                    <h6>{{ $item->product->name }}</h6>
                                                </div>
                                            </td>
                                            <td class="cart__price">
                                                Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                            </td>
                                            <td class="cart__close">
                                                {{-- Tombol Hapus dari Wishlist --}}
                                                <form action="{{ route('wishlist.toggle') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                                {{-- Tombol Tambah ke Keranjang --}}
                                                <a href="#" class="add-cart btn btn-primary btn-sm" data-product-id="{{ $item->product->id }}">Add To Cart</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h4>Wishlist Anda masih kosong.</h4>
                        <a href="{{ route('shop') }}" class="primary-btn mt-4">Mulai Belanja</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
