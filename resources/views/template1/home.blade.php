{{-- resources/views/template1/home.blade.php --}}
@extends('template1.layouts.template')

@section('content')

@push('styles')
    {{-- [TAMBAHAN] Style untuk Modal dan Notifikasi, disamakan dengan halaman Shop --}}
    <style>
        .toast-notification {
            position: fixed; bottom: 20px; right: 20px;
            background-color: #2c3e50; color: white;
            padding: 15px 25px; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10001; opacity: 0; visibility: hidden;
            transform: translateY(20px);
            transition: all 0.4s cubic-bezier(0.215, 0.610, 0.355, 1);
        }
        .toast-notification.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .toast-notification.success { background-color: #27ae60; }
        .toast-notification.error { background-color: #c0392b; }

        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6); display: none;
            align-items: center; justify-content: center; z-index: 10000;
            opacity: 0; visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .modal-overlay.show { display: flex; opacity: 1; visibility: visible; }
        .modal-content {
            background: white; padding: 1.5rem; border-radius: 12px;
            width: 90%; max-width: 400px;
            transform: scale(0.95); transition: transform 0.3s ease;
        }
        .modal-overlay.show .modal-content { transform: scale(1); }
        .modal-close {
            position: absolute; top: 10px; right: 15px;
            font-size: 2rem; font-weight: bold; color: #888; cursor: pointer;
        }
        .modal-variant-select {
            width: 100%; height: 46px; border: 1px solid #e1e1e1; padding: 0 15px;
            font-size: 14px; color: #444444; border-radius: 5px;
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px 12px;
        }
        .modal-variant-select:disabled { background-color: #f3f3f3; color: #b0b0b0; cursor: not-allowed; }
        .primary-btn:disabled { background-color: #b0b0b0 !important; cursor: not-allowed; }
        .product__item__text .add-cart {
            display: inline-block; width: auto; padding: 8px 25px; background: #111;
            color: #fff; border-radius: 20px; font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px; border: none;
            cursor: pointer; transition: all 0.3s;
        }
        .product__item__text .add-cart:hover { background: #333; }
    </style>
@endpush

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="hero__slider owl-carousel">
            {{-- Menggunakan loop untuk menampilkan semua hero yang aktif dari mitra --}}
            @forelse ($heroes as $hero)
                <div class="hero__items set-bg" data-setbg="{{ $hero->image_url }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    {{-- Data diambil dari objek $hero --}}
                                    <h6 id="preview-hero-subtitle">{{ $hero->subtitle }}</h6>
                                    <h2 id="preview-hero-title">{{ $hero->title }}</h2>
                                    <p id="preview-hero-description">{{ $hero->description }}</p>

                                    {{-- Tombol ini bisa diatur warnanya dari shop_settings jika mau --}}
                                    <a href="{{ $hero->button_url }}" class="primary-btn preview-button">
                                        <span id="preview-hero-button-text">{{ $hero->button_text }}</span>
                                        <span class="arrow_right"></span>
                                    </a>

                                    <div class="hero__social">
                                        {{-- Link sosial media bisa diambil dari $settings jika masih digunakan --}}
                                        <a href="#"><i class="fa fa-facebook"></i></a>
                                        <a href="#"><i class="fa fa-twitter"></i></a>
                                        <a href="#"><i class="fa fa-pinterest"></i></a>
                                        <a href="#"><i class="fa fa-instagram"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Konten default jika tidak ada hero yang diatur oleh mitra --}}
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
                {{-- Menggunakan loop untuk menampilkan banner dari mitra --}}
                @forelse ($banners as $banner)
                    {{-- Anda perlu menyesuaikan class col-lg-* berdasarkan jumlah banner --}}
                    <div class="col-lg-4">
                        <div class="banner__item">
                            <div class="banner__item__pic">
                                <img id="preview-banner-{{ $loop->index }}-img" src="{{ $banner->image_url }}"
                                    alt="{{ $banner->title }}">
                            </div>
                            <div class="banner__item__text">
                                <h2 id="preview-banner-{{ $loop->index }}-title">{{ $banner->title }}</h2>
                                <a href="{{ $banner->link_url }}">Shop now</a>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Konten default jika tidak ada banner --}}
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
                        {{-- Filter ini akan bekerja dengan class pada produk di bawah --}}
                        <li class="active" data-filter=".best-seller">Best Sellers</li>
                        <li data-filter=".new-arrival">New Arrivals</li>
                        <li data-filter=".hot-sale">Hot Sales</li>
                    </ul>
                </div>
            </div>

            @php
                // Menggabungkan semua produk dan memastikan tidak ada duplikat
                $allProducts = collect($bestSellers ?? [])
                    ->merge($newArrivals ?? [])
                    ->merge($hotSales ?? [])
                    ->unique('id');
            @endphp

            <div class="row product__filter">
                @forelse ($allProducts as $product)
                    @php
                        $classes = '';
                        if (($bestSellers ?? collect())->contains($product)) { $classes .= ' best-seller'; }
                        if (($newArrivals ?? collect())->contains($product)) { $classes .= ' new-arrival'; }
                        if (($hotSales ?? collect())->contains($product)) { $classes .= ' hot-sale'; }
                    @endphp
                    <div class="col-lg-3 col-md-6 col-sm-6 mix{{ $classes }}">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                @if ($product->is_new_arrival)
                                    <span class="label">New</span>
                                @elseif($product->is_hot_sale)
                                    <span class="label">Sale</span>
                                @endif
                                <ul class="product__hover">
                                    <li><a href="#"><img src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a></li>
                                    @if(isset($isPreview) && $isPreview)
                                        <li><a href="#"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a></li>
                                    @else
                                        <li><a href="{{ route('tenant.product.details', ['subdomain' => $subdomainName, 'product' => $product->slug]) }}"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a></li>
                                    @endif
                                </ul>
                            </div>
                            <div class="product__item__text">
                                <h6>{{ $product->name }}</h6>
                                
                                {{-- [PERBAIKAN] Tombol disamakan dengan halaman Shop --}}
                                <button type="button" class="add-cart open-variant-modal" data-product-id="{{ $product->id }}">
                                    + Add To Cart
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
    <div id="variant-modal" class="modal-overlay">
        <div class="modal-content relative">
            <span class="modal-close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
    <div id="toast-notification" class="toast-notification"></div>
@endsection

@push('scripts')
    {{-- [PERBAIKAN] JavaScript untuk Modal disamakan dengan halaman Shop --}}
    <script>
        const allProductData = @json($allProducts->keyBy('id')->map(function ($product) {
            $product->load('varians');
            $processedVariants = $product->varians->map(function ($varian) {
                $optionsMap = [];
                if (is_array($varian->options_data)) {
                    foreach ($varian->options_data as $option) {
                        $optionsMap[$option['name']] = $option['value'];
                    }
                }
                $varian->options_map = $optionsMap;
                return $varian;
            });
            $product->processed_varians = $processedVariants;
            return $product;
        }));

        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const isPreview = {{ $isPreview ?? false ? 'true' : 'false' }};
            const subdomain = '{{ $subdomainName ?? "" }}';

            if (typeof axios === 'undefined') {
                console.error('Axios tidak dimuat. AJAX tidak akan berfungsi.');
                return;
            }
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            
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

            function populateAndShowModal(product, modal, modalBody) {
                if (!product.processed_varians || product.processed_varians.length === 0) {
                    modalBody.innerHTML = `<h4 class="font-bold text-lg mb-4">${product.name}</h4><p class="text-gray-600">Produk ini tidak memiliki varian yang tersedia.</p><button type="button" class="primary-btn mt-3" onclick="document.getElementById('variant-modal').classList.remove('show')">Tutup</button>`;
                    modal.classList.add('show');
                    return;
                }

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

                const orderedOptionNames = Array.from(allOptionNames).sort((a, b) => {
                    if (a === 'Ukuran') return -1; if (b === 'Ukuran') return 1;
                    if (a === 'Warna') return -1; if (b === 'Warna') return 1;
                    return 0;
                });

                orderedOptionNames.forEach((optionName, index) => {
                    const uniqueOptionValues = [...new Set(product.processed_varians.flatMap(v => v.options_data.filter(opt => opt.name === optionName).map(opt => opt.value)))].filter(Boolean);
                    if (uniqueOptionValues.length > 0) {
                        const selectId = `option-${index}-${optionName.replace(/\s/g, '')}-select`;
                        const isDisabled = index > 0;
                        formHTML += `
                            <div>
                                <label for="${selectId}" class="block text-sm font-medium text-gray-700 mb-1">${optionName}</label>
                                <select id="${selectId}" name="option_${optionName.toLowerCase().replace(/\s/g, '_')}" class="modal-variant-select" data-option-name="${optionName}" ${isDisabled ? 'disabled' : ''} required>
                                    <option value="">Pilih ${optionName}</option>
                                    ${uniqueOptionValues.map(value => `<option value="${value}">${value}</option>`).join('')}
                                </select>
                            </div>`;
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
                    </form>`;

                modalBody.innerHTML = formHTML;
                modal.classList.add('show');
                attachModalEventListeners(product, orderedOptionNames);
            }

            function attachModalEventListeners(product, orderedOptionNames) {
                const form = document.getElementById('modal-cart-form');
                const modalAddBtn = document.getElementById('modal-add-btn');
                const modalDisplayPrice = document.getElementById('modal-display-price');
                const modalDisplayStock = document.getElementById('modal-display-stock');
                const selectedVariantIdInput = document.getElementById('selected-variant-id');
                const quantityInput = document.getElementById('quantity-input');
                const optionSelects = orderedOptionNames.map((name, index) => document.getElementById(`option-${index}-${name.replace(/\s/g, '')}-select`)).filter(Boolean);
                let currentSelectedVariant = null;

                const updateOptionDropdowns = () => {
                    let currentSelectedOptions = {};
                    optionSelects.forEach(select => { currentSelectedOptions[select.dataset.optionName] = select.value; });

                    optionSelects.forEach((currentSelect, currentIndex) => {
                        if (currentIndex > 0) {
                            const prevSelect = optionSelects[currentIndex - 1];
                            currentSelect.disabled = !prevSelect || !prevSelect.value;
                        }
                        if (!currentSelect.disabled && currentSelect.value === "") {
                            let availableValues = new Set();
                            const filteredVariants = product.processed_varians.filter(varian => {
                                let matches = true;
                                for (let i = 0; i < currentIndex; i++) {
                                    const prevSelect = optionSelects[i];
                                    if (prevSelect && prevSelect.value && varian.options_map[prevSelect.dataset.optionName] !== prevSelect.value) {
                                        matches = false; break;
                                    }
                                }
                                return matches;
                            });
                            filteredVariants.forEach(varian => {
                                if (varian.options_map[currentSelect.dataset.optionName]) {
                                    availableValues.add(varian.options_map[currentSelect.dataset.optionName]);
                                }
                            });
                            const currentValue = currentSelect.value;
                            currentSelect.innerHTML = `<option value="">Pilih ${currentSelect.dataset.optionName}</option>`;
                            Array.from(availableValues).sort().forEach(value => {
                                const hasStock = product.processed_varians.some(v => {
                                    let isMatch = true;
                                    for(const optName in currentSelectedOptions) {
                                        if (currentSelectedOptions[optName] && v.options_map[optName] !== currentSelectedOptions[optName]) {
                                            isMatch = false; break;
                                        }
                                    }
                                    return isMatch && v.options_map[currentSelect.dataset.optionName] === value && v.stock > 0;
                                });
                                const option = new Option(value, value);
                                if (!hasStock) {
                                    option.disabled = true; option.textContent += ' (Habis)';
                                }
                                currentSelect.add(option);
                            });
                            if (currentValue && Array.from(availableValues).includes(currentValue)) {
                                currentSelect.value = currentValue;
                            }
                        }
                    });
                    updateButtonState();
                };

                const updateButtonState = () => {
                    currentSelectedVariant = null;
                    let allOptionsSelected = true;
                    let selectedOptionsMap = {};
                    optionSelects.forEach(select => {
                        if (select.value === "") allOptionsSelected = false;
                        selectedOptionsMap[select.dataset.optionName] = select.value;
                    });

                    if (allOptionsSelected) {
                        currentSelectedVariant = product.processed_varians.find(v => {
                            let matches = true;
                            for (const optName in selectedOptionsMap) {
                                if (v.options_map[optName] !== selectedOptionsMap[optName]) {
                                    matches = false; break;
                                }
                            }
                            return matches;
                        });
                        if (currentSelectedVariant && currentSelectedVariant.stock > 0) {
                            modalAddBtn.disabled = false;
                            selectedVariantIdInput.value = currentSelectedVariant.id;
                            quantityInput.max = currentSelectedVariant.stock;
                            quantityInput.value = Math.min(quantityInput.value, currentSelectedVariant.stock);
                            modalDisplayPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(currentSelectedVariant.price);
                            modalDisplayStock.textContent = `Stok: ${currentSelectedVariant.stock}`;
                        } else {
                            modalAddBtn.disabled = true;
                            selectedVariantIdInput.value = '';
                            modalDisplayPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price);
                            modalDisplayStock.textContent = `Stok: ${currentSelectedVariant ? 'Habis' : '-'}`;
                        }
                    } else {
                        modalAddBtn.disabled = true;
                        selectedVariantIdInput.value = '';
                        modalDisplayPrice.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price);
                        modalDisplayStock.textContent = `Stok: -`;
                    }
                };

                optionSelects.forEach((select, index) => {
                    select.addEventListener('change', () => {
                        for (let i = index + 1; i < optionSelects.length; i++) {
                            optionSelects[i].value = "";
                            optionSelects[i].disabled = true;
                            optionSelects[i].innerHTML = `<option value="">Pilih ${optionSelects[i].dataset.optionName} dahulu</option>`;
                        }
                        updateOptionDropdowns();
                    });
                });
                quantityInput.addEventListener('input', updateButtonState);
                form.addEventListener('submit', handleFormSubmit);
                updateOptionDropdowns();
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = 'Memproses...';

                const postData = {
                    product_id: form.querySelector('input[name="product_id"]').value,
                    variant_id: form.querySelector('input[name="selected_variant_id"]').value,
                    quantity: form.querySelector('input[name="quantity"]').value
                };

                if (!postData.variant_id) {
                    showToast('Harap pilih varian terlebih dahulu.', 'error');
                    button.disabled = false;
                    button.innerHTML = 'Tambah ke Keranjang';
                    return;
                }

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
                        showToast(err.response?.data?.message || 'Terjadi kesalahan.', 'error');
                    })
                    .finally(() => {
                        button.disabled = false;
                        button.innerHTML = 'Tambah ke Keranjang';
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

