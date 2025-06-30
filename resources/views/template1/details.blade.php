@extends('template1.layouts.template1')

{{-- Gunakan nama produk sebagai judul halaman jika tersedia --}}
@section('title', isset($product) ? $product->name : 'Detail Produk')

@push('styles')
{{-- CSS untuk Notifikasi Toast (jika diperlukan untuk 'Add to Cart') --}}
<style>
    .toast-notification {
        position: fixed; bottom: 20px; right: 20px; background-color: #333;
        color: white; padding: 15px 25px; border-radius: 8px; z-index: 10000;
        opacity: 0; visibility: hidden; transform: translateY(20px);
        transition: all 0.3s ease-in-out; font-family: 'Nunito Sans', sans-serif;
    }
    .toast-notification.show { opacity: 1; visibility: visible; transform: translateY(0); }
    .toast-notification.success { background-color: #28a745; }
    .toast-notification.error { background-color: #dc3545; }
    .product__details__option .size label.active,
    .product__details__option .color label.active {
        border-color: #111111;
    }
</style>
@endpush

@section('content')

    <!-- Shop Details Section Begin -->
    <section class="shop-details">
        <div class="product__details__pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__breadcrumb">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('shop') }}">Shop</a>
                            <span>{{ $product->name ?? 'Detail Produk' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        {{-- Thumbnails --}}
                        <ul class="nav nav-tabs" role="tablist">
                            @if(isset($product->gallery) && count($product->gallery) > 0)
                                @foreach($product->gallery as $key => $image)
                                <li class="nav-item">
                                    <a class="nav-link {{ $key == 0 ? 'active' : '' }}" data-toggle="tab" href="#tabs-{{ $key + 1 }}" role="tab">
                                        <div class="product__thumb__pic set-bg" data-setbg="{{ $image->url }}"></div>
                                    </a>
                                </li>
                                @endforeach
                            @else
                                <li class="nav-item">
                                     <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">
                                        <div class="product__thumb__pic set-bg" data-setbg="{{ $product->image_url ?? asset('template1/img/shop-details/product-1.jpg') }}"></div>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-lg-6 col-md-9">
                        {{-- Main Image Display --}}
                        <div class="tab-content">
                             @if(isset($product->gallery) && count($product->gallery) > 0)
                                @foreach($product->gallery as $key => $image)
                                <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="tabs-{{ $key + 1 }}" role="tabpanel">
                                    <div class="product__details__pic__item">
                                        <img src="{{ $image->url }}" alt="">
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                    <div class="product__details__pic__item">
                                        <img src="{{ $product->image_url ?? asset('template1/img/shop-details/product-1.jpg') }}" alt="">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product__details__content">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-8">
                        <div class="product__details__text">
                            <h4>{{ $product->name ?? 'Nama Produk' }}</h4>
                            <div class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ ($product->rating ?? 0) >= $i ? 'fa-star' : (($product->rating ?? 0) > ($i - 1) ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                                <span> - {{ $product->reviews_count ?? 0 }} Reviews</span>
                            </div>
                            <h3>Rp {{ number_format($product->price, 0, ',', '.') }} 
                                @if(($product->discount_percentage ?? 0) > 0)
                                <span>Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                                @endif
                            </h3>
                            <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>
                            
                            <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                {{-- Pilihan Ukuran (Varian) --}}
                                <div class="product__details__option">
                                    <div class="product__details__option__size">
                                        <span>Size:</span>
                                        <label for="size-s" class="active"><input type="radio" id="size-s" name="size" value="s"> s</label>
                                        <label for="size-m"><input type="radio" id="size-m" name="size" value="m"> m</label>
                                        <label for="size-l"><input type="radio" id="size-l" name="size" value="l"> l</label>
                                        <label for="size-xl"><input type="radio" id="size-xl" name="size" value="xl"> xl</label>
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="product__details__cart__option">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input type="text" name="quantity" value="1">
                                        </div>
                                    </div>
                                    <button type="submit" class="primary-btn">add to cart</button>
                                </div>
                            </form>
                            <div class="product__details__btns__option">
                                <a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i class="fa fa-heart"></i> add to wishlist</a>
                            </div>

                            <div class="product__details__last__option">
                                <h5><span>Guaranteed Safe Checkout</span></h5>
                                <img src="{{ asset('template1/img/shop-details/details-payment.png') }}" alt="">
                                <ul>
                                    <li><span>SKU:</span> {{ $product->sku ?? 'N/A' }}</li>
                                    <li><span>Categories:</span> {{ $product->category->name ?? 'Uncategorized' }}</li>
                                    <li><span>Tag:</span> Clothes, Skin, Body</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tabs-5"
                                    role="tab">Deskripsi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-7" role="tab">Ulasan ({{ $product->reviews_count ?? 0 }})</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-5" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <p class="note">Deskripsi Lengkap</p>
                                        <div class="product__details__tab__text">
                                           {!! $product->description ?? 'Informasi deskripsi lengkap produk belum tersedia.' !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tabs-7" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <div class="product__details__tab__text">
                                            <p>Ulasan pelanggan akan ditampilkan di sini.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Details Section End -->

    <!-- Related Section Begin -->
    <section class="related spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="related-title">Produk Terkait</h3>
                </div>
            </div>
            <div class="row">
                @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                    @foreach($relatedProducts as $related)
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="{{ $related->image_url ?? '' }}">
                                @if ($related->is_new ?? false) <span class="label">New</span> @endif
                                <ul class="product__hover">
                                    <li><a href="#" class="toggle-wishlist" data-product-id="{{ $related->id }}"><img src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a></li>
                                    <li><a href="{{ route('shop.details', $related->slug) }}"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a></li>
                                </ul>
                            </div>
                            <div class="product__item__text">
                                <h6>{{ $related->name }}</h6>
                                <a href="#" class="add-cart" data-product-id="{{ $related->id }}">+ Add To Cart</a>
                                <div class="rating">
                                    @for ($i=1; $i<=5; $i++) <i class="fa fa-star{{ ($related->rating ?? 0) >= $i ? '' : '-o' }}"></i> @endfor
                                </div>
                                <h5>Rp {{ number_format($related->price, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-lg-12 text-center">
                        <p>Tidak ada produk terkait.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- Related Section End -->
    
    <div id="toast-notification" class="toast-notification"></div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi 'pro-qty' untuk tombol kuantitas
    var proQty = $('.pro-qty');
    proQty.prepend('<span class="fa fa-angle-up dec qtybtn"></span>');
    proQty.append('<span class="fa fa-angle-down inc qtybtn"></span>');
    proQty.on('click', '.qtybtn', function () {
        var $button = $(this);
        var oldValue = $button.parent().find('input').val();
        var newVal;
        if ($button.hasClass('inc')) {
            newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 1) { // Tidak boleh kurang dari 1
                newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 1;
            }
        }
        $button.parent().find('input').val(newVal);
    });

    // Fungsi untuk memilih ukuran
    $('.product__details__option__size label').on('click', function () {
        $('.product__details__option__size label').removeClass('active');
        $(this).addClass('active');
    });

    // --- Logika AJAX (Sama seperti halaman shop) ---
    const toastElement = document.getElementById('toast-notification');
    let toastTimeout;
    function showToast(message, type = 'success') {
        clearTimeout(toastTimeout);
        toastElement.textContent = message;
        toastElement.className = 'toast-notification';
        toastElement.classList.add(type, 'show');
        toastTimeout = setTimeout(() => {
            toastElement.classList.remove('show');
        }, 3000);
    }
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // AJAX untuk Add to Cart (menggunakan form)
    document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const originalButton = this.querySelector('.primary-btn');
        originalButton.disabled = true;
        originalButton.textContent = 'ADDING...';

        fetch(this.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: new URLSearchParams(formData).toString() // Kirim sebagai form data
        })
        .then(res => res.json())
        .then(data => {
             if (data.success) {
                showToast(data.message || 'Produk berhasil ditambahkan!', 'success');
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement && data.cart_count !== undefined) {
                    cartCountElement.textContent = data.cart_count;
                }
            } else {
                showToast(data.message || 'Gagal menambahkan produk.', 'error');
            }
        })
        .catch(err => showToast('Terjadi kesalahan. Coba lagi.', 'error'))
        .finally(() => {
            originalButton.disabled = false;
            originalButton.textContent = 'add to cart';
        });
    });

    // AJAX untuk Wishlist
    document.querySelectorAll('.toggle-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            // ... (Kode AJAX wishlist sama seperti di halaman shop)
        });
    });
});
</script>
@endpush
