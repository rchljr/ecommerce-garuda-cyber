{{-- resources/views/template1/home.blade.php --}}
@extends('template1.layouts.template')

@section('title', 'Beranda')

@push('styles')
    {{-- Style kustom untuk modal varian dan notifikasi --}}
    <style>
        /* PERBAIKAN: Menambahkan text-shadow untuk keterbacaan */
        .hero__text h6,
        .hero__text h2,
        .hero__text p {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
        }

        .banner__item__text h2 {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.show {
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

        .primary-btn:disabled {
            background-color: #b0b0b0;
            cursor: not-allowed;
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

        .filter__controls li {
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .filter__controls li.active {
            background-color: #111111;
            color: #ffffff;
        }

        .product__item__text .add-cart {
            display: block;
            width: 100%;
            padding: 10px;
            background: #f3f3f3;
            color: #111;
            font-weight: 700;
            border: none;
            border-radius: 5px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product__item__text .add-cart:hover {
            background: #ca1515;
            color: #ffffff;
        }

        .product__hover li i {
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

        .product__hover li:hover i {
            background: #ca1515;
            color: #fff;
            transform: rotate(360deg);
        }

        .product__hover a.toggle-wishlist.active i,
        #modal-wishlist-btn.active i {
            background: #ca1515;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $subdomainName = request()->route('subdomain');
    @endphp

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="hero__slider owl-carousel">
            @forelse ($heroes as $hero)
                <div class="hero__items set-bg" data-setbg="{{ $hero->image_url }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    <h6>{{ $hero->subtitle }}</h6>
                                    <h2>{{ $hero->title }}</h2>
                                    <p>{{ $hero->description }}</p>
                                    <a href="{{ $hero->button_url }}" class="primary-btn">{{ $hero->button_text }} <span
                                            class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="hero__items set-bg" data-setbg="{{ asset('template1/img/hero/hero-default.jpg') }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    <h6>Selamat Datang!</h6>
                                    <h2>Toko Fashion Terbaik Anda</h2>
                                    <p>Atur tampilan hero section Anda melalui dashboard editor.</p>
                                    <a href="#" class="primary-btn">Telusuri Sekarang <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                @forelse ($banners as $banner)
                    <div class="col-lg-4">
                        <div class="banner__item">
                            <div class="banner__item__pic">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                            </div>
                            <div class="banner__item__text">
                                <h2>{{ $banner->title }}</h2>
                                <a href="{{ $banner->link_url }}">Belanja Sekarang</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12 text-center">
                        <p>Banner belum diatur. Silakan tambahkan melalui dashboard.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="active" data-filter=".best-seller">Produk Terlaris</li>
                        <li data-filter=".new-arrival">Koleksi Terbaru</li>
                        <li data-filter=".hot-sale">Promo Spesial</li>
                    </ul>
                </div>
            </div>

            @php
                $allProducts = collect($bestSellers ?? [])
                    ->merge($newArrivals ?? [])
                    ->merge($hotSales ?? [])
                    ->unique('id');
            @endphp

            <div class="row product__filter">
                @forelse ($allProducts as $product)
                    @php
                        $classes = '';
                        if (($bestSellers ?? collect())->contains($product)) {
                            $classes .= ' best-seller';
                        }
                        if (($newArrivals ?? collect())->contains($product)) {
                            $classes .= ' new-arrival';
                        }
                        if (($hotSales ?? collect())->contains($product)) {
                            $classes .= ' hot-sale';
                        }
                    @endphp
                    <div class="col-lg-3 col-md-6 col-sm-6 mix{{ $classes }}">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                @if ($product->is_new_arrival)
                                    <span class="label">Baru</span>
                                @elseif($product->is_hot_sale)
                                    <span class="label">Diskon</span>
                                @endif
                                <ul class="product__hover">
                                    <li><a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i
                                                class="fa fa-heart-o"></i></a></li>
                                    <li><a
                                            href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $subdomainName, 'product' => $product->slug]) : '#' }}"><i
                                                class="fa fa-eye"></i></a></li>
                                </ul>
                            </div>
                            <div class="product__item__text">
                                <h6>{{ $product->name }}</h6>
                                {{-- PERBAIKAN KUNCI: Hanya menyimpan ID, data lengkap diambil dari objek JS global --}}
                                <button class="add-cart open-variant-modal" data-product-id="{{ $product->id }}">
                                    + Keranjang
                                </button>
                                <div class="rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa {{ ($product->rating_product ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </div>
                                <h5>{{ format_rupiah($product->price) }}</h5>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center py-5">Belum ada produk unggulan saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Modal Varian -->
    <div id="variant-modal" class="modal-overlay">
        <div class="modal-content relative">
            <span class="modal-close">&times;</span>
            <div id="modal-body">
                {{-- Konten modal akan diisi oleh JavaScript --}}
            </div>
        </div>
    </div>

    {{-- Notifikasi Toast --}}
    <div id="toast-notification" class="toast-notification"></div>
@endsection

@push('scripts')
    <script>
        const allProductData = @json($allProducts->mapWithKeys(function ($product) {
            return [$product->id => $product->load('variants')];
        }));

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('variant-modal');
            const modalBody = document.getElementById('modal-body');
            const toastElement = document.getElementById('toast-notification');
            let toastTimeout;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const isPreview = {{ $isPreview ? 'true' : 'false' }};
            const subdomain = '{{ $subdomainName }}';

            // Pastikan Axios tersedia sebelum melanjutkan
            if (typeof axios === 'undefined') {
                console.error('Axios library is not loaded. AJAX requests will fail.');
                return; // Hentikan eksekusi jika axios tidak ada
            }

            // Konfigurasi Axios untuk mengirim CSRF token secara otomatis di setiap request.
            if (csrfToken) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            } else {
                console.error('CSRF Token not found. AJAX requests will fail.');
            }

            // ===================================================================
            // FUNGSI BANTUAN (HELPERS)
            // ===================================================================

            function showToast(message, type = 'success') {
                clearTimeout(toastTimeout);
                toastElement.textContent = message;
                toastElement.className = 'toast-notification'; // Reset class
                toastElement.classList.add(type, 'show');
                toastTimeout = setTimeout(() => {
                    toastElement.classList.remove('show');
                }, 3000);
            }

            function closeModal() {
                modal.classList.remove('show');
            }

            // ===================================================================
            // LOGIKA UTAMA MODAL VARIAN
            // ===================================================================

            function populateAndShowModal(product) {
                const uniqueSizes = [...new Set(product.variants.map(v => v.size))];
                let sizesHTML = uniqueSizes.map(size => `<option value="${size}">${size}</option>`).join('');

                modalBody.innerHTML = `
                            <div>
                                <h4 class="font-bold text-lg">${product.name}</h4>
                                <p class="text-red-600 font-bold text-lg">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price)}</p>
                            </div>
                            <form id="modal-cart-form" class="space-y-4 mt-4" novalidate>
                                <input type="hidden" name="product_id" value="${product.id}">
                                <div>
                                    <label for="size-select" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                                    <select id="size-select" name="size" class="modal-variant-select" required>
                                        <option value="">Pilih Ukuran</option>
                                        ${sizesHTML}
                                    </select>
                                </div>
                                <div>
                                    <label for="color-select" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                                    <select id="color-select" name="color" class="modal-variant-select" disabled required>
                                        <option value="">Pilih ukuran terlebih dahulu</option>
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
            }

            function attachModalEventListeners(product) {
                const form = modalBody.querySelector('#modal-cart-form');
                const sizeSelect = modalBody.querySelector('#size-select');
                const colorSelect = modalBody.querySelector('#color-select');
                const modalAddBtn = modalBody.querySelector('#modal-add-btn');
                const modalWishlistBtn = modalBody.querySelector('#modal-wishlist-btn');

                function updateColors() {
                    const selectedSize = sizeSelect.value;
                    colorSelect.innerHTML = '<option value="">Pilih Warna</option>';
                    colorSelect.disabled = true;

                    if (selectedSize) {
                        const availableColors = product.variants.filter(v => v.size === selectedSize);
                        if (availableColors.length > 0) {
                            availableColors.forEach(variant => {
                                const option = document.createElement('option');
                                option.value = variant.color;
                                option.textContent = variant.color;
                                if (variant.stock <= 0) {
                                    option.disabled = true;
                                    option.textContent += ' (Stok Habis)';
                                }
                                colorSelect.appendChild(option);
                            });
                            colorSelect.disabled = false;
                        } else {
                            colorSelect.innerHTML = '<option value="">Warna tidak tersedia</option>';
                        }
                    } else {
                        colorSelect.innerHTML = '<option value="">Pilih ukuran terlebih dahulu</option>';
                    }
                    updateButtonState();
                }

                function updateButtonState() {
                    const selectedSize = sizeSelect.value;
                    const selectedColor = colorSelect.value;
                    if (selectedSize && selectedColor) {
                        const variant = product.variants.find(v => v.size === selectedSize && v.color === selectedColor);
                        modalAddBtn.disabled = !variant || variant.stock <= 0;
                    } else {
                        modalAddBtn.disabled = true;
                    }
                }

                sizeSelect.addEventListener('change', updateColors);
                colorSelect.addEventListener('change', updateButtonState);
                form.addEventListener('submit', handleAddToCart);
                modalWishlistBtn.addEventListener('click', () => handleToggleWishlist(modalWishlistBtn));
            }

            // ===================================================================
            // HANDLER UNTUK AKSI (CART & WISHLIST)
            // ===================================================================

            function handleAddToCart(e) {
                e.preventDefault();
                if (isPreview) {
                    showToast('Fitur ini tidak aktif dalam mode pratinjau.', 'error');
                    return;
                }
                const form = e.target;
                const submitButton = form.querySelector('#modal-add-btn');
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                submitButton.disabled = true;
                submitButton.textContent = 'Menambahkan...';
                axios.post(`/tenant/${subdomain}/cart/add`, data)
                    .then(response => {
                        if (response.data.success) {
                            showToast(response.data.message, 'success');
                            const cartCountEl = document.querySelector('.header__nav__option #cart-count');
                            if (cartCountEl) {
                                cartCountEl.textContent = response.data.cart_count;
                            }
                            closeModal();
                        } else {
                            throw new Error(response.data.message || 'Gagal menambahkan produk.');
                        }
                    })
                    .catch(error => {
                        const errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan.';
                        showToast(errorMessage, 'error');
                    })
                    .finally(() => {
                        if (document.body.contains(submitButton)) {
                            submitButton.disabled = false;
                            submitButton.textContent = 'Tambah ke Keranjang';
                        }
                    });
            }

            function handleToggleWishlist(button) {
                if (isPreview) {
                    showToast('Fitur ini tidak aktif dalam mode pratinjau.', 'error');
                    return;
                }
                if (!csrfToken) {
                    showToast('Terjadi kesalahan. Coba refresh halaman.', 'error');
                    return;
                }
                const productId = button.dataset.productId;
                axios.post(`/tenant/${subdomain}/wishlist/toggle`, { product_id: productId })
                    .then(response => {
                        if (response.data.success) {
                            const allWishlistButtons = document.querySelectorAll(`.toggle-wishlist[data-product-id="${productId}"], #modal-wishlist-btn[data-product-id="${productId}"]`);
                            if (response.data.action === 'added') {
                                showToast('Produk ditambahkan ke Wishlist!', 'success');
                                allWishlistButtons.forEach(btn => btn.classList.add('active'));
                            } else {
                                showToast('Produk dihapus dari Wishlist.', 'success');
                                allWishlistButtons.forEach(btn => btn.classList.remove('active'));
                            }
                            const wishlistCountEl = document.querySelector('.header__nav__option #wishlist-count');
                            if (wishlistCountEl) {
                                wishlistCountEl.textContent = response.data.wishlist_count;
                            }
                        } else {
                            throw new Error(response.data.message || 'Operasi wishlist gagal.');
                        }
                    })
                    .catch(error => {
                        if (error.response?.status === 401) {
                            showToast('Silakan login untuk menggunakan wishlist.', 'error');
                            setTimeout(() => {
                                window.location.href = `/tenant/${subdomain}/customer/login`;
                            }, 1500);
                        } else {
                            const errorMessage = error.response?.data?.message || 'Terjadi kesalahan pada wishlist.';
                            showToast(errorMessage, 'error');
                        }
                    });
            }

            // ===================================================================
            // EVENT LISTENERS AWAL
            // ===================================================================

            document.querySelectorAll('.open-variant-modal').forEach(button => {
                button.addEventListener('click', function () {
                    // PERBAIKAN KUNCI: Mengambil data dari objek global, bukan dari atribut
                    const productId = this.dataset.productId;
                    const productData = allProductData[productId];

                    if (productData) {
                        populateAndShowModal(productData);
                    } else {
                        console.error("Data produk tidak ditemukan untuk ID:", productId);
                        showToast("Tidak dapat memuat detail produk.", "error");
                    }
                });
            });

            modal.addEventListener('click', function (e) {
                if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
                    closeModal();
                }
            });

            document.querySelectorAll('.toggle-wishlist').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    handleToggleWishlist(this);
                });
            });
        });
    </script>
@endpush