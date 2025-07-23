@extends('template1.layouts.template')

@section('title', 'Wishlist Saya')

@push('styles')
    {{-- Style yang konsisten untuk Notifikasi dan Modal Varian --}}
    <style>
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

        .primary-btn:disabled {
            background-color: #b0b0b0 !important;
            cursor: not-allowed;
        }

        /* Gaya untuk tombol aksi di tabel */
        .wishlist-actions .primary-btn,
        .wishlist-actions .outline-btn {
            padding: 8px 15px;
            font-size: 13px;
            margin: 5px;
            border-radius: 20px;
            cursor: pointer;
            border: 1px solid #111;
        }

        .wishlist-actions .primary-btn {
            background: #111;
            color: #fff;
        }

        .wishlist-actions .outline-btn {
            background: transparent;
            color: #111;
        }

        .wishlist-actions .outline-btn:hover {
            background: #111;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp

    <!-- Breadcrumb -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Wishlist Saya</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('tenant.home', ['subdomain' => $currentSubdomain]) }}">Beranda</a>
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
            <div id="wishlist-content">
                @if(isset($wishlistItems) && $wishlistItems->isNotEmpty())
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="shopping__cart__table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($wishlistItems as $item)
                                            @if($item->product) {{-- Pastikan produk masih ada --}}
                                                <tr data-wishlist-row="{{ $item->product->id }}">
                                                    <td class="product__cart__item">
                                                        {{-- <div class="product__cart__item__pic">
                                                            <img src="{{ $item->product->image_url ?? '' }}"
                                                                alt="{{ $item->product->name }}" style="width: 90px;">
                                                        </div> --}}
                                                        <div class="product__cart__item__text">
                                                            <h6>{{ $item->product->name }}</h6>
                                                        </div>
                                                    </td>
                                                    <td class="cart__price">
                                                        {{ format_rupiah($item->product->price) }}
                                                    </td>
                                                    <td class="cart__close text-center wishlist-actions">
                                                        <button class="primary-btn open-variant-modal"
                                                            data-product-id="{{ $item->product->id }}">+ Keranjang</button>
                                                        <button class="outline-btn remove-wishlist-item"
                                                            data-product-id="{{ $item->product->id }}">Hapus</button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-lg-12 text-center" id="empty-wishlist-message">
                            <h4>Wishlist Anda masih kosong.</h4>
                            <a href="{{ route('tenant.shop', ['subdomain' => $currentSubdomain]) }}"
                                class="primary-btn mt-4">Mulai Belanja</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Toast & Modal Placeholders -->
    <div id="toast-notification" class="toast-notification"></div>
    <div id="variant-modal" class="modal-overlay">
        <div class="modal-content relative">
            <span class="modal-close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Data Produk Global dari Wishlist
        const allProductData = @json($wishlistItems->mapWithKeys(function ($item) {
            if (!$item->product)
                return []; // Lewati jika produk tidak ada

            $product = $item->product;
            $product->load('varians'); // Pastikan relasi 'varians' dimuat

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

            return [$product->id => $product];
        }));

        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const subdomain = '{{ $currentSubdomain }}';

            if (typeof axios === 'undefined') {
                console.error('Axios tidak dimuat.');
                return;
            }
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

            initializeWishlistActions();
            initializeModal();

            function initializeWishlistActions() {
                document.querySelectorAll('.remove-wishlist-item').forEach(button => {
                    button.addEventListener('click', e => {
                        e.preventDefault();
                        handleRemoveFromWishlist(button);
                    });
                });
            }

            function handleRemoveFromWishlist(button) {
                const productId = button.dataset.productId;

                axios.post(`/tenant/${subdomain}/wishlist/toggle`, { product_id: productId })
                    .then(res => {
                        if (res.data.success && res.data.action === 'removed') {
                            showToast('Produk dihapus dari wishlist.', 'success');

                            const wishlistCountEl = document.getElementById('wishlist-count');
                            if (wishlistCountEl) wishlistCountEl.textContent = res.data.wishlist_count;

                            const row = document.querySelector(`tr[data-wishlist-row="${productId}"]`);
                            if (row) {
                                row.style.transition = 'opacity 0.5s ease';
                                row.style.opacity = '0';
                                setTimeout(() => {
                                    row.remove();
                                    if (document.querySelector('tbody').children.length === 0) {
                                        const wishlistContent = document.getElementById('wishlist-content');
                                        wishlistContent.innerHTML = `
                                                <div class="row">
                                                    <div class="col-lg-12 text-center">
                                                        <h4>Wishlist Anda masih kosong.</h4>
                                                        <a href="{{ route('tenant.shop', ['subdomain' => $currentSubdomain]) }}" class="primary-btn mt-4">Mulai Belanja</a>
                                                    </div>
                                                </div>`;
                                    }
                                }, 500);
                            }
                        } else {
                            throw new Error(res.data.message || 'Gagal menghapus produk.');
                        }
                    })
                    .catch(err => {
                        showToast(err.response?.data?.message || 'Terjadi kesalahan.', 'error');
                    });
            }

            function initializeModal() {
                const modal = document.getElementById('variant-modal');
                const modalBody = document.getElementById('modal-body');

                document.querySelectorAll('.open-variant-modal').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
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
                            <input type="hidden" name="selected_variant_id" id="selected-variant-id" value="">`;
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
                                    for (const optName in currentSelectedOptions) {
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