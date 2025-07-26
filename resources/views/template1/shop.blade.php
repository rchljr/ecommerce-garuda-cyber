@extends('template1.layouts.template')

{{-- Menggunakan variabel $shop yang dikirim dari controller --}}
@section('title', 'Daftar Produk' . optional($shop)->name)

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
                                    {{ $products->total() }} hasil
                                </p>
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
        // Menggunakan PHP untuk memuat data produk dan variannya, termasuk options_data
        const allProductData = @json($products->keyBy('id')->map(function ($product) {
            $product->load('varians'); // Muat relasi varians
            // Proses varian untuk membuat peta opsi agar mudah diakses di JS
            $processedVariants = $product->varians->map(function ($varian) {
                $optionsMap = [];
                if (is_array($varian->options_data)) {
                    foreach ($varian->options_data as $option) {
                        $optionsMap[$option['name']] = $option['value'];
                    }
                }
                $varian->options_map = $optionsMap; // Tambahkan peta opsi ke objek varian
                return $varian;
            });
            $product->processed_varians = $processedVariants; // Lampirkan varian yang diproses ke produk
            return $product;
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

            // Fungsi utama untuk mengisi dan menampilkan modal varian
            function populateAndShowModal(product, modal, modalBody) {
                // Pastikan produk memiliki varian
                if (!product.processed_varians || product.processed_varians.length === 0) {
                    modalBody.innerHTML = `<h4 class="font-bold text-lg mb-4">${product.name}</h4><p class="text-gray-600">Produk ini tidak memiliki varian yang tersedia.</p><button type="button" class="primary-btn mt-3" onclick="document.getElementById('variant-modal').classList.remove('show')">Tutup</button>`;
                    modal.classList.add('show');
                    return;
                }

                // Kumpulkan semua nama opsi unik (misal: "Ukuran", "Warna") dari semua varian
                const allOptionNames = new Set();
                product.processed_varians.forEach(v => {
                    if (v.options_data && Array.isArray(v.options_data)) {
                        v.options_data.forEach(opt => allOptionNames.add(opt.name));
                    }
                });

                let formHTML = `
                        <div>
                            <h4 class="font-bold text-lg">${product.name}</h4>
                            <p class="text-red-600 font-bold text-lg" id="modal-display-price">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price)}</p>
                            <p class="text-gray-500 text-sm mt-1" id="modal-display-stock">Pilih varian untuk melihat stok</p>
                        </div>
                        <form id="modal-cart-form" class="space-y-4 mt-4" novalidate>
                            <input type="hidden" name="product_id" value="${product.id}">
                            <input type="hidden" name="selected_variant_id" id="selected-variant-id" value="">
                    `;

                // Urutan opsi: prioritaskan "Ukuran" lalu "Warna", sisanya sesuai urutan ditemukan
                const orderedOptionNames = Array.from(allOptionNames).sort((a, b) => {
                    if (a === 'Ukuran') return -1;
                    if (b === 'Ukuran') return 1;
                    if (a === 'Warna') return -1;
                    if (b === 'Warna') return 1;
                    return 0;
                });

                orderedOptionNames.forEach((optionName, index) => {
                    // Ambil nilai unik untuk opsi saat ini
                    const uniqueOptionValues = [...new Set(product.processed_varians.flatMap(v =>
                        v.options_data.filter(opt => opt.name === optionName).map(opt => opt.value)
                    ))].filter(Boolean); // Filter(Boolean) untuk hapus undefined/null

                    if (uniqueOptionValues.length > 0) {
                        const selectId = `option-${index}-${optionName.replace(/\s/g, '')}-select`;
                        const isDisabled = index > 0; // Dropdown pertama aktif, sisanya disable dulu

                        formHTML += `
                                <div>
                                    <label for="${selectId}" class="block text-sm font-medium text-gray-700 mb-1">${optionName}</label>
                                    <select id="${selectId}" name="option_${optionName.toLowerCase().replace(/\s/g, '_')}"
                                            class="modal-variant-select" data-option-name="${optionName}"
                                            ${isDisabled ? 'disabled' : ''} required>
                                        <option value="">Pilih ${optionName}</option>
                                        ${uniqueOptionValues.map(value => `<option value="${value}">${value}</option>`).join('')}
                                    </select>
                                </div>
                            `;
                    }
                });

                formHTML += `
                            <div class="mt-6">
                                <label for="quantity-input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <input type="number" name="quantity" id="quantity-input" value="1" min="1" class="w-full border-gray-300 rounded-md" required>
                            </div>
                            <div class="flex items-center space-x-2 mt-4">
                                <button type="submit" id="modal-add-btn" class="primary-btn w-full !bg-gray-800 !text-white hover:!bg-black" disabled>Tambah ke Keranjang</button>
                            </div>
                        </form>
                    `;

                modalBody.innerHTML = formHTML;
                modal.classList.add('show');
                attachModalEventListeners(product, orderedOptionNames); // Kirim product dan urutan nama opsi
            }

            function attachModalEventListeners(product, orderedOptionNames) {
                const form = document.getElementById('modal-cart-form');
                const modalAddBtn = document.getElementById('modal-add-btn');
                const modalDisplayPrice = document.getElementById('modal-display-price');
                const modalDisplayStock = document.getElementById('modal-display-stock');
                const selectedVariantIdInput = document.getElementById('selected-variant-id');
                const quantityInput = document.getElementById('quantity-input');

                // Dapatkan semua elemen select opsi
                const optionSelects = orderedOptionNames.map((name, index) =>
                    document.getElementById(`option-${index}-${name.replace(/\s/g, '')}-select`)
                ).filter(Boolean); // Filter null jika ada

                let currentSelectedOptions = {}; // { 'Ukuran': 'S', 'Warna': 'Merah' }
                let currentSelectedVariant = null;

                const updateOptionDropdowns = () => {
                    currentSelectedOptions = {};
                    optionSelects.forEach(select => {
                        currentSelectedOptions[select.dataset.optionName] = select.value;
                    });

                    // Aktifkan/nonaktifkan dropdown berikutnya dan isi ulang opsinya
                    optionSelects.forEach((currentSelect, currentIndex) => {
                        if (currentIndex > 0) {
                            const prevSelect = optionSelects[currentIndex - 1];
                            currentSelect.disabled = !prevSelect || !prevSelect.value;
                        }

                        // Jika dropdown aktif dan bukan yang pertama, saring opsinya
                        if (!currentSelect.disabled && currentSelect.value === "") {
                            let availableValuesForCurrentSelect = new Set();

                            // Filter varian yang cocok dengan pilihan sebelumnya
                            const filteredVariants = product.processed_varians.filter(varian => {
                                let matchesPrevious = true;
                                for (let i = 0; i < currentIndex; i++) {
                                    const prevSelect = optionSelects[i];
                                    if (prevSelect && prevSelect.value && varian.options_map[prevSelect.dataset.optionName] !== prevSelect.value) {
                                        matchesPrevious = false;
                                        break;
                                    }
                                }
                                return matchesPrevious;
                            });

                            // Kumpulkan nilai yang tersedia untuk dropdown saat ini dari varian yang difilter
                            filteredVariants.forEach(varian => {
                                if (varian.options_map[currentSelect.dataset.optionName]) {
                                    availableValuesForCurrentSelect.add(varian.options_map[currentSelect.dataset.optionName]);
                                }
                            });

                            const currentValue = currentSelect.value; // Simpan nilai yang dipilih
                            currentSelect.innerHTML = `<option value="">Pilih ${currentSelect.dataset.optionName}</option>`;
                            Array.from(availableValuesForCurrentSelect).sort().forEach(value => {
                                // Cek stok untuk opsi ini
                                const hasStock = product.processed_varians.some(v => {
                                    let isMatch = true;
                                    for (const optName in currentSelectedOptions) {
                                        if (currentSelectedOptions[optName] && v.options_map[optName] !== currentSelectedOptions[optName]) {
                                            isMatch = false;
                                            break;
                                        }
                                    }
                                    return isMatch && v.options_map[currentSelect.dataset.optionName] === value && v.stock > 0;
                                });

                                const option = new Option(value, value);
                                if (!hasStock) {
                                    option.disabled = true;
                                    option.textContent += ' (Habis)';
                                }
                                currentSelect.add(option);
                            });
                            // Kembalikan nilai yang dipilih jika masih valid
                            if (currentValue && Array.from(availableValuesForCurrentSelect).includes(currentValue)) {
                                currentSelect.value = currentValue;
                            }
                        }
                    });

                    updateButtonState();
                };

                const updateButtonState = () => {
                    currentSelectedVariant = null;
                    let allOptionsSelected = true;
                    let selectedOptionsMap = {}; // Untuk mencari varian yang cocok

                    optionSelects.forEach(select => {
                        if (select.value === "") {
                            allOptionsSelected = false;
                        }
                        selectedOptionsMap[select.dataset.optionName] = select.value;
                    });

                    if (allOptionsSelected) {
                        // Cari varian yang cocok dengan semua pilihan
                        currentSelectedVariant = product.processed_varians.find(v => {
                            let matches = true;
                            for (const optName in selectedOptionsMap) {
                                if (v.options_map[optName] !== selectedOptionsMap[optName]) {
                                    matches = false;
                                    break;
                                }
                            }
                            return matches;
                        });

                        if (currentSelectedVariant && currentSelectedVariant.stock > 0) {
                            modalAddBtn.disabled = false;
                            selectedVariantIdInput.value = currentSelectedVariant.id;
                            quantityInput.max = currentSelectedVariant.stock; // Set max quantity
                            quantityInput.value = Math.min(quantityInput.value, currentSelectedVariant.stock); // Adjust current quantity if too high
                            modalDisplayPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(currentSelectedVariant.price);
                            modalDisplayStock.textContent = `Stok: ${currentSelectedVariant.stock}`;
                        } else {
                            modalAddBtn.disabled = true;
                            selectedVariantIdInput.value = '';
                            modalDisplayPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price); // Kembali ke harga produk jika varian habis/tidak ditemukan
                            modalDisplayStock.textContent = `Stok: ${currentSelectedVariant ? 'Habis' : '-'}`;
                        }
                    } else {
                        modalAddBtn.disabled = true;
                        selectedVariantIdInput.value = '';
                        modalDisplayPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price); // Kembali ke harga produk
                        modalDisplayStock.textContent = `Stok: -`;
                    }
                };

                // Attach event listeners to all dynamically created select elements
                optionSelects.forEach((select, index) => {
                    select.addEventListener('change', () => {
                        // Reset dropdowns setelah yang saat ini
                        for (let i = index + 1; i < optionSelects.length; i++) {
                            optionSelects[i].value = "";
                            optionSelects[i].disabled = true;
                            optionSelects[i].innerHTML = `<option value="">Pilih ${optionSelects[i].dataset.optionName} dahulu</option>`; // Reset teks placeholder
                        }
                        updateOptionDropdowns(); // Perbarui semua dropdown lagi
                    });
                });

                quantityInput.addEventListener('input', updateButtonState); // Perbarui state tombol saat quantity berubah

                form.addEventListener('submit', e => handleFormSubmit(e));
                // Modal wishlist button (if exists)
                // const modalWishlistBtn = document.getElementById('modal-wishlist-btn');
                // if (modalWishlistBtn) modalWishlistBtn.addEventListener('click', () => handleToggleWishlist(modalWishlistBtn));

                updateOptionDropdowns(); // Panggil pertama kali untuk menginisialisasi
            }

            // Fungsi ini akan menerima variant_id dari modal
            function handleFormSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = 'Memproses...';

                const productId = form.querySelector('input[name="product_id"]').value;
                const variantId = form.querySelector('input[name="selected_variant_id"]').value;
                const quantity = form.querySelector('input[name="quantity"]').value;

                if (!variantId) {
                    showToast('Harap pilih varian terlebih dahulu.', 'error');
                    button.disabled = false;
                    button.innerHTML = 'Tambah ke Keranjang';
                    return;
                }

                const postData = {
                    product_id: productId,
                    variant_id: variantId, // Kirim ID varian yang dipilih
                    quantity: quantity
                };

                axios.post(`/tenant/${subdomain}/cart/add`, postData)
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
                    .catch(err => {
                        console.error("Cart Add Error:", err.response || err);
                        showToast(err.response?.data?.message || 'Terjadi kesalahan.', 'error');
                    })
                    .finally(() => {
                        button.disabled = false;
                        button.innerHTML = 'Tambah ke Keranjang';
                    });
            }

            function initializeWishlist() {
                document.querySelectorAll('.toggle-wishlist').forEach(button => {
                    button.addEventListener('click', e => {
                        e.preventDefault();
                        handleToggleWishlist(button);
                    });
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