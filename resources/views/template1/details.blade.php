@extends('template1.layouts.template')

{{-- Gunakan nama produk sebagai judul halaman jika tersedia --}}
@section('title', isset($product) ? $product->name : 'Detail Produk')

@push('styles')
    {{-- CSS untuk Notifikasi Toast dan Varian Aktif --}}
    <style>
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
            font-family: 'Nunito Sans', sans-serif;
        }

        .toast-notification.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .toast-notification.success {
            background-color: #28a745;
        }

        .toast-notification.error {
            background-color: #dc3545;
        }

        .product__details__option .size label.active,
        .product__details__option .color label.active {
            border: 2px solid #111111;
        }

        .product__details__option__color label {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
            position: relative;
            cursor: pointer;
        }

        .product__details__option__color label input {
            position: absolute;
            visibility: hidden;
        }

        /* Style untuk ikon wishlist yang aktif */
        .product__details__btns__option .toggle-wishlist.active i,
        .product__hover .toggle-wishlist.active img {
            filter: invert(25%) sepia(100%) saturate(5000%) hue-rotate(330deg);
        }
    </style>
@endpush

@section('content')
    @php
        // Variabel ini tidak lagi diperlukan karena rute sudah ditangani oleh grup domain
        // $isPreview = $isPreview ?? false;
        // $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    <!-- Shop Details Section Begin -->
    <section class="shop-details">
        
        <div class="product__details__pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__breadcrumb">
                            {{-- PERBAIKAN: Hapus parameter 'subdomain' --}}
                            <a href="{{ route('tenant.home') }}">Home</a>
                            <a href="{{ route('tenant.shop') }}">Shop</a>
                            <span>{{ $product->name ?? 'Detail Produk' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        {{-- Thumbnails --}}
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabs-main" role="tab">
                                    <div class="product__thumb__pic set-bg" data-setbg="{{ $product->image_url }}"></div>
                                </a>
                            </li>
                            @if (isset($product->gallery) && $product->gallery->count() > 0)
                                @foreach ($product->gallery as $key => $image)
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tabs-{{ $key }}"
                                            role="tab">
                                            <div class="product__thumb__pic set-bg" data-setbg="{{ $image->image_url }}">
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="col-lg-6 col-md-9">
                        {{-- Main Image Display --}}
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabs-main" role="tabpanel">
                                <div class="product__details__pic__item">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                </div>
                            </div>
                            @if (isset($product->gallery) && $product->gallery->count() > 0)
                                @foreach ($product->gallery as $key => $image)
                                    <div class="tab-pane" id="tabs-{{ $key }}" role="tabpanel">
                                        <div class="product__details__pic__item">
                                            <img src="{{ $image->image_url }}"
                                                alt="{{ $product->name }} - Gallery Image {{ $key + 1 }}">
                                        </div>
                                    </div>
                                @endforeach
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
                                    <i
                                        class="fa {{ ($product->rating ?? 0) >= $i ? 'fa-star' : (($product->rating ?? 0) > $i - 1 ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                                <span> - {{ $product->reviews_count ?? 0 }} Reviews</span>
                            </div>
                            <h3>Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
                            <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                            {{-- PERBAIKAN: Hapus parameter 'subdomain' --}}
                            <form id="add-to-cart-form"
                                action="{{ route('tenant.cart.add') }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                @php
                                    $uniqueColors = $product->variants->pluck('color')->unique();
                                    $uniqueSizes = $product->variants->pluck('size')->unique();
                                @endphp

                                <div class="product__details__option">
                                    <div class="product__details__option__size">
                                        <span>Size:</span>
                                        @foreach ($uniqueSizes as $size)
                                            <label for="size-{{ $size }}"><input type="radio"
                                                    id="size-{{ $size }}" name="size"
                                                    value="{{ $size }}"> {{ $size }}</label>
                                        @endforeach
                                    </div>
                                    <div class="product__details__option__color">
                                        <span>Color:</span>
                                        @foreach ($uniqueColors as $color)
                                            @php
                                                $cssColor = strtolower($color);
                                                $colorMap = [
                                                    'putih' => 'white',
                                                    'hitam' => 'black',
                                                    'merah' => 'red',
                                                    'biru' => 'blue',
                                                    'hijau' => 'green',
                                                    'kuning' => 'yellow',
                                                    'abu-abu' => 'gray',
                                                ];
                                                $displayColor = $colorMap[$cssColor] ?? $cssColor;
                                            @endphp
                                            <label for="color-{{ $color }}"
                                                style="background-color: {{ $displayColor }}; border: 1px solid #ccc;">
                                                <input type="radio" id="color-{{ $color }}" name="color"
                                                    value="{{ $color }}">
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

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
                                <a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i
                                        class="fa fa-heart"></i> add to wishlist</a>
                            </div>

                            <div class="product__details__last__option">
                                <h5><span>Guaranteed Safe Checkout</span></h5>
                                <img src="{{ asset('template1/img/shop-details/details-payment.png') }}" alt="">
                                <ul>
                                    <li><span>SKU:</span> {{ $product->sku ?? 'N/A' }}</li>
                                    <li><span>Categories:</span> {{ $product->subCategory->name ?? 'Uncategorized' }}</li>
                                    <li><span>Tag:</span>
                                        @forelse($product->tags as $tag)
                                            {{ $tag->name }}{{ !$loop->last ? ',' : '' }}
                                        @empty
                                            -
                                        @endforelse
                                    </li>
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
                                    <a class="nav-link active" data-toggle="tab" href="#tabs-description"
                                        role="tab">Deskripsi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-reviews" role="tab">Ulasan
                                        ({{ $product->reviews_count ?? 0 }})</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-description" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <div class="product__details__tab__text">
                                            {!! $product->description ?? 'Informasi deskripsi lengkap produk belum tersedia.' !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tabs-reviews" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <p>Ulasan pelanggan akan ditampilkan di sini.</p>
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
    {{-- ... (Bagian Produk Terkait tetap sama) ... --}}
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
            proQty.on('click', '.qtybtn', function() {
                var $button = $(this);
                var oldValue = $button.parent().find('input').val();
                var newVal;
                if ($button.hasClass('inc')) {
                    newVal = parseFloat(oldValue) + 1;
                } else {
                    if (oldValue > 1) {
                        newVal = parseFloat(oldValue) - 1;
                    } else {
                        newVal = 1;
                    }
                }
                $button.parent().find('input').val(newVal);
            });

            // Fungsi untuk memilih varian
            $('.product__details__option__size label, .product__details__option__color label').on('click',
            function() {
                $(this).siblings().removeClass('active');
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

                const selectedSize = this.querySelector('input[name="size"]:checked');
                const selectedColor = this.querySelector('input[name="color"]:checked');

                if (!selectedSize || !selectedColor) {
                    showToast('Silakan pilih Ukuran dan Warna terlebih dahulu.', 'error');
                    return;
                }

                const formData = new FormData(this);
                const originalButton = this.querySelector('.primary-btn');
                originalButton.disabled = true;
                originalButton.textContent = 'ADDING...';

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json', // Beri tahu server kita mengirim JSON
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(Object.fromEntries(
                            formData)) // Ubah form data menjadi JSON
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(errorData => {
                                const errorMessages = Object.values(errorData.errors).flat()
                                    .join('\n');
                                throw new Error(errorMessages);
                            });
                        }
                        return res.json();
                    })
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
                    .catch(err => {
                        showToast(err.message, 'error');
                    })
                    .finally(() => {
                        originalButton.disabled = false;
                        originalButton.textContent = 'add to cart';
                    });
            });

            // AJAX untuk Wishlist
            document.querySelectorAll('.toggle-wishlist').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!csrfToken) {
                        showToast('Terjadi kesalahan. Coba refresh halaman.', 'error');
                        return;
                    }

                    const productId = this.dataset.productId;

                    // PERBAIKAN: Hapus parameter 'subdomain'
                    fetch("{{ route('tenant.wishlist.toggle') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => {
                            if (response.status === 401) {
                                // PERBAIKAN: Hapus parameter 'subdomain'
                                window.location.href =
                                    "{{ route('tenant.customer.login.form') }}";
                                throw new Error('Unauthorized');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                if (data.action === 'added') {
                                    showToast('Produk ditambahkan ke Wishlist!', 'success');
                                    this.classList.add('active');
                                } else {
                                    showToast('Produk dihapus dari Wishlist.', 'success');
                                    this.classList.remove('active');
                                }
                                const wishlistCountElement = document.getElementById(
                                    'wishlist-count');
                                if (wishlistCountElement) {
                                    wishlistCountElement.textContent = data.wishlist_count;
                                }
                            } else {
                                showToast(data.message || 'Operasi wishlist gagal.', 'error');
                            }
                        }).catch(error => {
                            if (error.message !== 'Unauthorized') {
                                console.error('Wishlist Error:', error);
                                showToast('Terjadi kesalahan pada wishlist.', 'error');
                            }
                        });
                });
            });
        });
    </script>
@endpush
