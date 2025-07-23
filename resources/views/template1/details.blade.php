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

        /* Gaya untuk pilihan varian yang aktif */
        .product__details__option .size label.active,
        .product__details__option__color label.active {
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
            border: 1px solid #ccc;
        }

        .product__details__option__color label input,
        .product__details__option__size label input {
            position: absolute;
            visibility: hidden;
        }

        /* Style untuk ikon wishlist yang aktif */
        .product__details__btns__option .toggle-wishlist.active i {
            color: #e53636;
        }

        /* Style untuk tombol Add to Cart yang dinonaktifkan */
        .primary-btn:disabled {
            background-color: #b0b0b0;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    <!-- Shop Details Section Begin -->
    <section class="shop-details">
        <div class="product__details__pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__breadcrumb">
                            <a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a>
                            <a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Shop</a>
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
                            @if(isset($product->gallery) && $product->gallery->count() > 0)
                                @foreach($product->gallery as $key => $image)
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tabs-{{ $key }}" role="tab">
                                            <div class="product__thumb__pic set-bg" data-setbg="{{ $image->image_url }}"></div>
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
                            @if(isset($product->gallery) && $product->gallery->count() > 0)
                                @foreach($product->gallery as $key => $image)
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
                                        class="fa {{ ($product->rating ?? 0) >= $i ? 'fa-star' : (($product->rating ?? 0) > ($i - 1) ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                                <span> - {{ $product->reviews_count ?? 0 }} Reviews</span>
                            </div>
                            <h3>Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
                            <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                            @if($product->varians && $product->varians->count() > 0)
                                <form id="add-to-cart-form"
                                    action="{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                                    @php
                                        // [PERBAIKAN] Gunakan 'varians' (dengan 's') sesuai nama relasi di Model
                                        $uniqueColors = $product->varians->pluck('color')->unique()->filter();
                                        $allVariants = $product->varians;
                                    @endphp

                                    <div class="product__details__option">
                                        @if($uniqueColors->count() > 0)
                                            <div class="product__details__option__color">
                                                <span>Color:</span>
                                                @foreach($uniqueColors as $color)
                                                    <label for="color-{{$color}}" style="background-color: {{ strtolower($color) }};">
                                                        <input type="radio" id="color-{{$color}}" name="color" value="{{$color}}">
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="product__details__option__size">
                                            <span>Size:</span>
                                            <div id="size-options-container">
                                                <p class="text-muted small" style="display: inline-block;">
                                                    {{ $uniqueColors->count() > 0 ? 'Pilih warna terlebih dahulu' : 'Pilih ukuran' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product__details__cart__option">
                                        <div class="quantity">
                                            <div class="pro-qty">
                                                <input type="text" name="quantity" value="1">
                                            </div>
                                        </div>
                                        <button type="submit" class="primary-btn" id="add-to-cart-btn" disabled>add to
                                            cart</button>
                                    </div>
                                </form>
                            @else
                                <div class="my-4 p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                                    Produk ini sedang tidak tersedia atau tidak memiliki varian.
                                </div>
                            @endif
                            <div class="product__details__btns__option">
                                <a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i
                                        class="fa fa-heart"></i> add to wishlist</a>
                            </div>

                            {{-- <div class="product__details__last__option">
                                <h5><span>Guaranteed Safe Checkout</span></h5>
                                <img src="{{ asset('template1/img/shop-details/details-payment.png') }}" alt="">
                                <ul>
                                    <li><span>SKU:</span> <span id="variant-sku">{{ $product->sku ?? 'N/A' }}</span></li>
                                    <li><span>Categories:</span> {{ $product->subCategory->name ?? 'Uncategorized' }}</li>
                                    <li><span>Tag:</span>
                                        @forelse($product->tags as $tag)
                                            {{ $tag->name }}{{ !$loop->last ? ',' : '' }}
                                        @empty
                                            -
                                        @endforelse
                                    </li>
                                </ul>
                            </div> --}}
                        </div>
                    </div>
                </div>
                {{-- ... (Tabs Deskripsi & Ulasan) ... --}}
            </div>
        </div>
    </section>
    <!-- Shop Details Section End -->

    <div id="toast-notification" class="toast-notification"></div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data varian dari PHP ke JavaScript
            const allVariants = @json($allVariants);

            const colorRadios = document.querySelectorAll('input[name="color"]');
            const sizeContainer = document.getElementById('size-options-container');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const selectedVariantInput = document.getElementById('selected-variant-id');
            const skuElement = document.getElementById('variant-sku');

            let selectedColor = null;
            let selectedSize = null;

            function updateCartButtonState() {
                if (selectedColor && selectedSize) {
                    const variant = allVariants.find(v => v.color === selectedColor && v.size === selectedSize);
                    if (variant && variant.stock > 0) {
                        addToCartBtn.disabled = false;
                        addToCartBtn.textContent = 'add to cart';
                        selectedVariantInput.value = variant.id;
                        skuElement.textContent = variant.sku || 'N/A';
                    } else {
                        addToCartBtn.disabled = true;
                        addToCartBtn.textContent = 'Stok Habis';
                        selectedVariantInput.value = '';
                        skuElement.textContent = 'N/A';
                    }
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Pilih Varian';
                    selectedVariantInput.value = '';
                    skuElement.textContent = 'N/A';
                }
            }

            colorRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Reset active class
                    document.querySelectorAll('.product__details__option__color label').forEach(l => l.classList.remove('active'));
                    this.parentElement.classList.add('active');

                    selectedColor = this.value;
                    selectedSize = null; // Reset pilihan ukuran

                    const availableSizes = allVariants.filter(v => v.color === selectedColor);

                    sizeContainer.innerHTML = '';
                    if (availableSizes.length > 0) {
                        availableSizes.forEach(item => {
                            const isDisabled = item.stock <= 0;
                            const optionHtml = `
                                        <label for="size-${item.size}" class="${isDisabled ? 'disabled' : ''}">
                                            <input type="radio" id="size-${item.size}" name="size" value="${item.size}" ${isDisabled ? 'disabled' : ''}> ${item.size}
                                        </label>
                                    `;
                            sizeContainer.innerHTML += optionHtml;
                        });
                    } else {
                        sizeContainer.innerHTML = '<p class="text-muted small">Tidak ada ukuran tersedia.</p>';
                    }
                    updateCartButtonState();
                });
            });

            sizeContainer.addEventListener('click', function (e) {
                if (e.target.tagName === 'LABEL' && !e.target.classList.contains('disabled')) {
                    sizeContainer.querySelectorAll('label').forEach(l => l.classList.remove('active'));
                    e.target.classList.add('active');
                    const radio = e.target.querySelector('input[name="size"]');
                    if (radio) {
                        radio.checked = true;
                        selectedSize = radio.value;
                        updateCartButtonState();
                    }
                }
            });

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

            document.getElementById('add-to-cart-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const originalButton = this.querySelector('.primary-btn');

                originalButton.disabled = true;
                originalButton.innerHTML = 'ADDING...';

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(response => {
                        const { status, body } = response;
                        if (status >= 400) {
                            const errorMessages = body.errors ? Object.values(body.errors).flat().join('\n') : (body.message || 'Terjadi kesalahan.');
                            throw new Error(errorMessages);
                        }

                        if (body.success) {
                            showToast(body.message || 'Produk berhasil ditambahkan!', 'success');
                            const cartCountElement = document.getElementById('cart-count');
                            if (cartCountElement && body.cart_count !== undefined) {
                                cartCountElement.textContent = body.cart_count;
                            }
                        } else {
                            throw new Error(body.message || 'Gagal menambahkan produk.');
                        }
                    })
                    .catch(err => {
                        showToast(err.message, 'error');
                    })
                    .finally(() => {
                        originalButton.disabled = false;
                        originalButton.innerHTML = 'add to cart';
                    });
            });

            document.querySelectorAll('.toggle-wishlist').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (!csrfToken) {
                        showToast('Terjadi kesalahan. Coba refresh halaman.', 'error');
                        return;
                    }

                    const productId = this.dataset.productId;
                    const wishlistButton = this;

                    fetch("{{ !$isPreview ? route('tenant.wishlist.toggle', ['subdomain' => $currentSubdomain]) : '#' }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                        .then(response => {
                            if (response.status === 401) {
                                window.location.href = "{{ !$isPreview ? route('tenant.customer.login.form', ['subdomain' => $currentSubdomain]) : '#' }}";
                                throw new Error('Unauthorized');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                if (data.action === 'added') {
                                    showToast('Produk ditambahkan ke Wishlist!', 'success');
                                    wishlistButton.classList.add('active');
                                } else {
                                    showToast('Produk dihapus dari Wishlist.', 'success');
                                    wishlistButton.classList.remove('active');
                                }
                                const wishlistCountElement = document.getElementById('wishlist-count');
                                if (wishlistCountElement && data.wishlist_count !== undefined) {
                                    wishlistCountElement.textContent = data.wishlist_count;
                                }
                            } else {
                                throw new Error(data.message || 'Operasi wishlist gagal.');
                            }
                        }).catch(error => {
                            if (error.message !== 'Unauthorized') {
                                console.error('Wishlist Error:', error);
                                showToast(error.message || 'Terjadi kesalahan pada wishlist.', 'error');
                            }
                        });
                });
            });

            var proQty = $('.pro-qty');
            proQty.prepend('<span class="fa fa-angle-down dec qtybtn"></span>');
            proQty.append('<span class="fa fa-angle-up inc qtybtn"></span>');
            proQty.on('click', '.qtybtn', function () {
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
        });
    </script>
@endpush