@extends('template1.layouts.template1')

@section('title', 'Keranjang Belanja')

@section('content')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Keranjang Belanja</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('shop') }}">Shop</a>
                            <span>Keranjang Belanja</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Shopping Cart Section Begin -->
    <section class="shopping-cart spad">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (!empty($cartItems))
                <div class="row">
                    <div class="col-lg-8">
                        <div class="shopping__cart__table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kuantitas</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $cartTotal = 0; @endphp
                                    @foreach ($cartItems as $id => $item)
                                        @php
                                            $itemTotal = $item['price'] * $item['quantity'];
                                            $cartTotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td class="product__cart__item">
                                                <div class="product__cart__item__pic">
                                                    <img src="{{ $item['image'] ?? asset('template1/img/product/product-1.jpg') }}" alt="{{ $item['name'] }}" style="width: 90px;">
                                                </div>
                                                <div class="product__cart__item__text">
                                                    <h6>{{ $item['name'] }}</h6>
                                                    <h5>Rp {{ number_format($item['price'], 0, ',', '.') }}</h5>
                                                </div>
                                            </td>
                                            <td class="quantity__item">
                                                <div class="quantity">
                                                    {{-- Form untuk update quantity --}}
                                                    <form action="{{ route('cart.update', $id) }}" method="POST" id="update-form-{{ $id }}" class="d-flex align-items-center">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="pro-qty-2">
                                                            <input type="text" name="quantity" value="{{ $item['quantity'] }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary ml-2">Update</button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td class="cart__price">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                            <td class="cart__close">
                                                {{-- Form untuk hapus item --}}
                                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus item ini?')" style="border:none; background:none; cursor:pointer;">
                                                        <i class="fa fa-close"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="continue__btn">
                                    <a href="{{ route('shop') }}">Lanjutkan Belanja</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="cart__discount">
                            <h6>Kode Diskon</h6>
                            <form action="#">
                                <input type="text" placeholder="Kode kupon">
                                <button type="submit">Terapkan</button>
                            </form>
                        </div>
                        <div class="cart__total">
                            <h6>Total Keranjang</h6>
                            <ul>
                                <li>Subtotal <span>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span></li>
                                <li>Total <span>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span></li>
                            </ul>
                            <a href="#" class="primary-btn">Lanjut ke checkout</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="cart_empty_area py-5">
                            <img src="{{ asset('template1/img/shopping-cart.png') }}" alt="Empty Cart" class="mb-4" style="max-width: 150px; margin-left:auto; margin-right:auto;">
                            <h2>Keranjang Belanja Anda Kosong</h2>
                            <p>Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
                            <a href="{{ route('shop') }}" class="primary-btn cart-btn mt-4">Mulai Belanja</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <!-- Shopping Cart Section End -->
@endsection
