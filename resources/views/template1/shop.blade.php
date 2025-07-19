@extends('template1.layouts.template')

{{-- Menggunakan variabel $shop yang dikirim dari controller --}}
@section('title', 'Daftar Produk - ' . optional($shop)->name)

@push('styles')
    {{-- CSS untuk Price Slider, Notifikasi, dan Modal --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.1/nouislider.min.css">
    <style>
        /* Gaya Dasar */
        .sidebar__categories .section-title,
        .shop__product__option {
            margin-top: 30px;
        }

        .sidebar__categories ul li a {
            color: #555;
        }

        .sidebar__categories ul li a.active {
            color: #111;
            font-weight: 700;
        }

        .filter-range-wrap .price-input {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .filter-range-wrap .price-input input {
            border: none;
            background: transparent;
            width: 48%;
            font-size: 14px;
            color: #555;
        }

        .filter-range-wrap .price-input input#maxamount {
            text-align: right;
        }

        #price-range-slider .noUi-connect {
            background: #111;
        }

        .sidebar__filter .primary-btn {
            background: #111;
            border-radius: 20px;
        }

        .sidebar__filter .primary-btn:hover {
            background: #333;
        }

        .product__item__text {
            padding: 20px 15px;
            text-align: center;
        }

        .product__item__text h6 {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin-bottom: 8px;
            font-size: 15px;
            font-weight: 600;
        }

        .product__item__text h6 a {
            color: #1c1c1c;
        }

        .product__item__text .rating {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin-bottom: 8px;
            font-size: 12px;
            color: #f7941d;
        }

        .product__item__text h5 {
            margin-bottom: 12px;
            color: #111;
            font-weight: 700;
        }

        .product__item__text .add-cart {
            display: inline-block;
            width: auto;
            padding: 8px 25px;
            background: #111;
            color: #fff;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .product__item__text .add-cart:hover {
            background: #333;
        }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2c3e50;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.4s cubic-bezier(0.215, 0.610, 0.355, 1);
        }

        .toast-notification.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .toast-notification.success {
            background-color: #27ae60;
        }

        .toast-notification.error {
            background-color: #c0392b;
        }

        .product__hover a.toggle-wishlist.active i,
        #modal-wishlist-btn.active i {
            background: #111;
            color: #fff;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 2rem;
            font-weight: bold;
            color: #888;
            cursor: pointer;
        }

        .modal-variant-select {
            width: 100%;
            height: 46px;
            border: 1px solid #e1e1e1;
            padding: 0 15px;
            font-size: 14px;
            color: #444444;
            border-radius: 5px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .modal-variant-select:disabled {
            background-color: #f3f3f3;
            color: #b0b0b0;
            cursor: not-allowed;
        }

        .primary-btn:disabled,
        .site-btn:disabled {
            background-color: #b0b0b0 !important;
            cursor: not-allowed;
        }

        .product__item__pic .product__hover li i {
            color: #111;
            font-size: 18px;
            background: #fff;
            display: block;
            height: 45px;
            width: 45px;
            line-height: 45px;
            text-align: center;
            border-radius: 50%;
            transition: all .3s;
        }

        .product__item__pic .product__hover li:hover i {
            background: #111;
            color: #fff;
            transform: rotate(360deg);
        }

        /* PERBAIKAN RESPONSIVE */
        @media (max-width: 991px) {
            .shop__sidebar {
                margin-bottom: 40px;
            }
        }

        @media (max-width: 767px) {

            /* Membuat filter & urutkan menjadi vertikal di mobile */
            .shop__product__option .row>.col-sm-6 {
                width: 100%;
                text-align: center;
            }

            .shop__product__option .select__option {
                float: none;
                margin-top: 15px;
                display: inline-block;
            }

            .breadcrumb__text h4 {
                font-size: 28px;
            }

            .product__item__text h6 {
                font-size: 14px;
            }
        }

        /* Perbaikan tampilan daftar kategori */
        .sidebar__categories ul {
            list-style-type: none;
            padding-left: 0;
        }

        .sidebar__categories ul li a {
            padding: 8px 0;
            display: block;
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Toko</h4>
                        <div class="breadcrumb__links">
                            <a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                            <span>Toko</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                {{-- Sidebar Filter --}}
                <div class="col-lg-3 col-md-12"> {{-- PERBAIKAN: Menggunakan col-md-12 agar full width di tablet --}}
                    <div class="shop__sidebar">
                        <div class="sidebar__categories">
                            <div class="section-title">
                                <h4>Kategori</h4>
                            </div>
                            <ul>
                                <li><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}"
                                        class="{{ !request('category') ? 'active' : '' }}">Semua Kategori</a></li>
                                @forelse ($categories as $subCategory)
                                    <li><a href="{{ !$isPreview ? route('tenant.shop', array_merge(request()->except('page'), ['subdomain' => $currentSubdomain, 'category' => $subCategory->slug])) : '#' }}"
                                            class="{{ request('category') == $subCategory->slug ? 'active' : '' }}">{{ $subCategory->name }}</a>
                                    </li>
                                @empty
                                    <li><a>Tidak ada kategori</a></li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="sidebar__filter">
                            <div class="section-title">
                                <h4>Filter Harga</h4>
                            </div>
                            <form id="filter-form" method="GET"
                                action="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">
                                @if(request('category'))
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                @endif
                                @if(request('sort'))
                                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                                @endif
                                <div class="filter-range-wrap">
                                    <div id="price-range-slider"></div>
                                    <div class="range-slider">
                                        <div class="price-input">
                                            <input type="text" id="minamount" readonly>
                                            <input type="text" id="maxamount" readonly>
                                        </div>
                                        <input type="hidden" id="min_price_hidden" name="min_price">
                                        <input type="hidden" id="max_price_hidden" name="max_price">
                                    </div>
                                </div>
                                <button type="submit" class="primary-btn mt-3" style="width: 100%; border: none;" {{ $isPreview ? 'disabled' : '' }}>Filter</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Daftar Produk --}}
                <div class="col-lg-9 col-md-12"> {{-- PERBAIKAN: Menggunakan col-md-12 agar full width di tablet --}}
                    <div class="shop__product__option">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <p>Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari
                                    {{ $products->total() }} hasil</p>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="select__option">
                                    <form id="sort-form" method="GET"
                                        action="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">
                                        @if(request('category')) <input type="hidden" name="category"
                                        value="{{ request('category') }}"> @endif
                                        @if(request('min_price')) <input type="hidden" name="min_price"
                                        value="{{ request('min_price') }}"> @endif
                                        @if(request('max_price')) <input type="hidden" name="max_price"
                                        value="{{ request('max_price') }}"> @endif
                                        <select name="sort" id="sort-by" onchange="this.form.submit()" {{ $isPreview ? 'disabled' : '' }}>
                                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                                Harga: Rendah ke Tinggi</option>
                                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @forelse ($products as $product)
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item mb-4">
                                    <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                        @if ($product->is_new_arrival)
                                            <span class="label">Baru</span>
                                        @elseif($product->is_hot_sale)
                                            <span class="label sale">Diskon</span>
                                        @endif
                                        <ul class="product__hover">
                                            <li><a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i
                                                        class="fa fa-heart-o"></i></a></li>
                                            <li><a
                                                    href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}"><i
                                                        class="fa fa-eye"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="product__item__text">
                                        <h6><a
                                                href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}">{{ $product->name }}</a>
                                        </h6>
                                        <div class="rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fa {{ ($product->rating_product ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                            @endfor
                                        </div>
                                        <h5>{{ format_rupiah($product->price) }}</h5>
                                        <button class="add-cart open-variant-modal" data-product-id="{{ $product->id }}">+
                                            Keranjang</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-lg-12 text-center py-5">
                                <h4>Tidak Ada Produk yang Cocok</h4>
                                <p>Coba sesuaikan filter atau kembali lagi nanti.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            {{ $products->withQueryString()->links('vendor.pagination.default') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Toast & Modal -->
    <div id="toast-notification" class="toast-notification"></div>
    <div id="variant-modal" class="modal-overlay">
        <div class="modal-content relative">
            <span class="modal-close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script tidak berubah, jadi saya singkat untuk kejelasan --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.1/nouislider.min.js"></script>
    <script>
        const allProductData = @json($products->keyBy('id')->map(function ($product) {
            return $product->load('variants');
        }));

        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const isPreview = {{ $isPreview ? 'true' : 'false' }};
            const subdomain = '{{ $currentSubdomain }}';

            if (typeof axios === 'undefined') {
                console.error('Axios tidak dimuat. AJAX tidak akan berfungsi.');
                return;
            }
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

            initializePriceSlider();
            initializeModal();
            initializeWishlist();

            function initializePriceSlider() {
                if (isPreview) return;
                const slider = document.getElementById('price-range-slider');
                if (!slider) return;

                const minAmountInput = document.getElementById('minamount');
                const maxAmountInput = document.getElementById('maxamount');
                const minPriceHidden = document.getElementById('min_price_hidden');
                const maxPriceHidden = document.getElementById('max_price_hidden');

                const minPrice = parseInt("{{ request('min_price', 10000) }}");
                const maxPrice = parseInt("{{ request('max_price', 500000) }}");

                noUiSlider.create(slider, {
                    start: [minPrice, maxPrice],
                    connect: true,
                    range: { 'min': 10000, 'max': 500000 },
                    format: { to: value => Math.round(value), from: value => Number(value) }
                });

                slider.noUiSlider.on('update', function (values, handle) {
                    const numericValue = parseInt(values[handle]);
                    if (handle === 0) {
                        minAmountInput.value = 'Rp ' + numericValue.toLocaleString('id-ID');
                        minPriceHidden.value = numericValue;
                    } else {
                        maxAmountInput.value = 'Rp ' + numericValue.toLocaleString('id-ID');
                        maxPriceHidden.value = numericValue;
                    }
                });
            }

            function initializeModal() {
                const modal = document.getElementById('variant-modal');
                const modalBody = document.getElementById('modal-body');

                document.querySelectorAll('.open-variant-modal').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (isPreview) {
                            showToast('Fitur ini tidak tersedia dalam mode pratinjau.', 'error');
                            return;
                        }
                        const productId = this.dataset.productId;
                        const productData = allProductData[productId];
                        if (productData) {
                            populateAndShowModal(productData, modal, modalBody);
                        } else {
                            showToast('Gagal memuat detail produk.', 'error');
                        }
                    });
                });

                modal.addEventListener('click', e => {
                    if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
                        modal.classList.remove('show');
                    }
                });
            }

            function populateAndShowModal(product, modal, modalBody) {
                const uniqueSizes = [...new Set(product.variants.map(v => v.size).filter(Boolean))];
                let sizesHTML = uniqueSizes.map(size => `<option value="${size}">${size}</option>`).join('');

                modalBody.innerHTML = `
                        <div>
                            <h4 class="font-bold text-lg">${product.name}</h4>
                            <p class="text-red-600 font-bold text-lg">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price)}</p>
                        </div>
                        <form id="modal-cart-form" class="space-y-4 mt-4" novalidate>
                            <input type="hidden" name="product_id" value="${product.id}">
                            ${sizesHTML ? `<div>
                                <label for="size-select" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                                <select id="size-select" name="size" class="modal-variant-select" required>
                                    <option value="">Pilih Ukuran</option>
                                    ${sizesHTML}
                                </select>
                            </div>` : ''}
                            <div>
                                <label for="color-select" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                                <select id="color-select" name="color" class="modal-variant-select" ${!sizesHTML ? '' : 'disabled'} required>
                                    <option value="">${sizesHTML ? 'Pilih ukuran dahulu' : 'Pilih Warna'}</option>
                                </select>
                            </div>
                            <div class="mt-6">
                                <label for="quantity-input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <input type="number" name="quantity" id="quantity-input" value="1" min="1" class="w-full border-gray-300 rounded-md" required>
                            </div>
                            <div class="flex items-center space-x-2 mt-4">
                                <button type="submit" id="modal-add-btn" class="primary-btn w-full !bg-gray-800 !text-white hover:!bg-black" disabled>Tambah ke Keranjang</button>
                            </div>
                        </form>
                    `;
                modal.classList.add('show');
                attachModalEventListeners(product);
                if (!sizesHTML) {
                    const colorSelect = document.getElementById('color-select');
                    const availableColors = [...new Set(product.variants.map(v => v.color).filter(Boolean))];
                    availableColors.forEach(color => colorSelect.add(new Option(color, color)));
                    colorSelect.disabled = false;
                }
            }

            function attachModalEventListeners(product) {
                const form = document.getElementById('modal-cart-form');
                const sizeSelect = document.getElementById('size-select');
                const colorSelect = document.getElementById('color-select');
                const modalAddBtn = document.getElementById('modal-add-btn');
                const modalWishlistBtn = document.getElementById('modal-wishlist-btn');

                const updateColors = () => {
                    const selectedSize = sizeSelect.value;
                    colorSelect.innerHTML = '<option value="">Pilih Warna</option>';
                    colorSelect.disabled = true;
                    if (selectedSize) {
                        const availableColors = [...new Set(product.variants.filter(v => v.size === selectedSize).map(v => v.color))];
                        if (availableColors.length > 0) {
                            availableColors.forEach(color => {
                                const variant = product.variants.find(v => v.size === selectedSize && v.color === color);
                                const option = new Option(color, color);
                                if (variant.stock <= 0) {
                                    option.disabled = true;
                                    option.textContent += ' (Habis)';
                                }
                                colorSelect.add(option);
                            });
                            colorSelect.disabled = false;
                        } else {
                            colorSelect.innerHTML = '<option value="">Warna tidak ada</option>';
                        }
                    } else {
                        colorSelect.innerHTML = '<option value="">Pilih ukuran dahulu</option>';
                    }
                    updateButtonState();
                };

                const updateButtonState = () => {
                    const hasSize = !!sizeSelect;
                    const sizeValue = hasSize ? sizeSelect.value : true;
                    const colorValue = colorSelect.value;
                    modalAddBtn.disabled = !(sizeValue && colorValue);
                };

                if (sizeSelect) sizeSelect.addEventListener('change', updateColors);
                colorSelect.addEventListener('change', updateButtonState);
                form.addEventListener('submit', e => handleFormSubmit(e));
                modalWishlistBtn.addEventListener('click', () => handleToggleWishlist(modalWishlistBtn));
                updateButtonState();
            }

            function initializeWishlist() {
                document.querySelectorAll('.toggle-wishlist').forEach(button => {
                    button.addEventListener('click', e => {
                        e.preventDefault();
                        handleToggleWishlist(button);
                    });
                });
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = 'Memproses...';

                const data = Object.fromEntries(new FormData(form).entries());

                axios.post(`/tenant/${subdomain}/cart/add`, data)
                    .then(res => {
                        if (res.data.success) {
                            showToast(res.data.message, 'success');
                            const cartCountEl = document.getElementById('cart-count');
                            if (cartCountEl) cartCountEl.textContent = res.data.cart_count;
                            document.getElementById('variant-modal').classList.remove('show');
                        } else {
                            throw new Error(res.data.message);
                        }
                    })
                    .catch(err => showToast(err.response?.data?.message || 'Terjadi kesalahan.', 'error'))
                    .finally(() => {
                        button.disabled = false;
                        button.innerHTML = 'Tambah ke Keranjang';
                    });
            }

            function handleToggleWishlist(button) {
                if (isPreview) {
                    showToast('Fitur ini tidak tersedia dalam mode pratinjau.', 'error');
                    return;
                }
                const productId = button.dataset.productId;
                axios.post(`/tenant/${subdomain}/wishlist/toggle`, { product_id: productId })
                    .then(res => {
                        if (res.data.success) {
                            const allWishlistButtons = document.querySelectorAll(`.toggle-wishlist[data-product-id="${productId}"], #modal-wishlist-btn[data-product-id="${productId}"]`);
                            const added = res.data.action === 'added';
                            showToast(added ? 'Ditambahkan ke wishlist!' : 'Dihapus dari wishlist.', 'success');
                            allWishlistButtons.forEach(btn => btn.classList.toggle('active', added));
                            const wishlistCountEl = document.getElementById('wishlist-count');
                            if (wishlistCountEl) wishlistCountEl.textContent = res.data.wishlist_count;
                        } else {
                            throw new Error(res.data.message || 'Gagal memproses wishlist.');
                        }
                    })
                    .catch(err => {
                        console.error("Wishlist Error:", err.response || err);
                        if (err.response?.status === 401) {
                            showToast('Silakan login untuk memakai wishlist.', 'error');
                        } else {
                            showToast(err.response?.data?.message || 'Terjadi kesalahan wishlist.', 'error');
                        }
                    });
            }

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast-notification');
                clearTimeout(window.toastTimeout);
                toast.textContent = message;
                toast.className = `toast-notification ${type} show`;
                window.toastTimeout = setTimeout(() => toast.classList.remove('show'), 3000);
            }
        });
    </script>
@endpush