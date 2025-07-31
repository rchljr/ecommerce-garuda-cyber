@extends('template2.layouts.template')

@section('title', $product->name ?? 'Detail Produk')

@push('styles')
    {{-- CSS untuk Notifikasi Toast, Varian Aktif, dan Galeri Gambar --}}
    <style>
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 10001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
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

        .variant-options .form-group {
            margin-bottom: 15px;
        }

        .variant-options label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        .variant-options select {
            width: 100%;
            height: 40px;
            padding: 0 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .variant-options select:disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }

        .btn-black:disabled {
            background-color: #b0b0b0 !important;
            border-color: #b0b0b0 !important;
            cursor: not-allowed;
        }

        #thumbnail-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        #thumbnail-container img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 4px;
            transition: border-color 0.2s;
        }

        #thumbnail-container img.active,
        #thumbnail-container img:hover {
            border-color: #82ae46;
        }

        .star-rating .ion-ios-star {
            color: #ffc107;
        }

        .star-rating .ion-ios-star-outline {
            color: #ccc;
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    {{-- Breadcrumbs --}}
    <div class="hero-wrap hero-bread" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    <p class="breadcrumbs">
                        <span class="mr-2"><a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a></span>
                        <span class="mr-2"><a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Produk</a></span>
                        <span>Detail Produk</span>
                    </p>
                    <h1 class="mb-0 bread">{{ $product->name }}</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                {{-- Kolom Gambar Produk --}}
                <div class="col-lg-6 mb-5 ftco-animate">
                    <div id="main-image-display">
                        <a href="{{ $product->image_url }}" class="image-popup"><img src="{{ $product->image_url }}"
                                class="img-fluid" alt="{{ $product->name }}"></a>
                    </div>
                    <div id="thumbnail-container"></div>
                </div>

                {{-- Kolom Info & Opsi Produk --}}
                <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                    <h3>{{ $product->name }}</h3>

                    {{-- ✅ Menampilkan Rating & Ulasan --}}
                    <div class="star-rating d-flex">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="ion-ios-star{{ ($averageRating ?? 0) >= $i ? '' : '-outline' }}"></i>
                        @endfor
                        <span class="ml-2">{{ $reviewCount ?? 0 }} Ulasan</span>
                    </div>

                    <p class="price"><span id="product-display-price">{{ format_rupiah($product->price) }}</span></p>
                    <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                    <form id="add-to-cart-form" method="POST"
                        action="{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                        {{-- ✅ Menampilkan Varian Produk --}}
                        <div class="variant-options mt-4" id="dynamic-variant-options">
                            @if ($product->varians->isEmpty())
                                <p class="text-muted">Produk ini tidak memiliki varian.</p>
                            @endif
                        </div>

                        <div class="row mt-4">
                            <div class="input-group col-md-6 d-flex mb-3">
                                <span class="input-group-btn mr-2">
                                    <button type="button" class="quantity-left-minus btn" data-type="minus"><i
                                            class="ion-ios-remove"></i></button>
                                </span>
                                <input type="text" id="quantity" name="quantity" class="form-control input-number"
                                    value="1" min="1" max="100">
                                <span class="input-group-btn ml-2">
                                    <button type="button" class="quantity-right-plus btn" data-type="plus"><i
                                            class="ion-ios-add"></i></button>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <p id="stock_info" style="color: #000;">Stok tersedia: <span
                                    id="variant-stock">{{ $product->varians->first()->stock ?? ($product->stock ?? 0) }}</span>
                            </p>
                        </div>

                        <p>
                            <button type="submit" id="add-to-cart-btn"
                                class="btn btn-dark d-flex align-items-center gap-2 px-4 py-3 fw-semibold"
                                {{ $product->varians->isEmpty() ? '' : 'disabled' }}>
                                <i class="fas fa-shopping-cart"></i>
                                Tambah ke Keranjang
                            </button>
                        </p>
                    </form>
                </div>
            </div>

            {{-- Deskripsi, Ulasan, dan Tags --}}
            <div class="row mt-5">
                <div class="col-md-12">
                    <h5>Deskripsi</h5>
                    <p>{!! $product->description ?? 'Deskripsi lengkap produk akan muncul di sini.' !!}</p>
                </div>
                <hr>
                <div class="col-md-12">
                    <h5>Ulasan Pelanggan ({{ $reviewCount ?? 0 }})</h5>
                    @forelse ($reviews ?? [] as $review)
                        <div class="review border-bottom mb-3 pb-3">
                            <div class="desc">
                                <h4>
                                    <span class="text-left">{{ $review->name }}</span>
                                    <span
                                        class="float-right text-muted small">{{ $review->created_at->format('d M Y') }}</span>
                                </h4>
                                <p class="star">
                                    <span>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="ion-ios-star{{ $review->rating >= $i ? '' : '-outline' }}"></i>
                                        @endfor
                                    </span>
                                </p>
                                <p>{{ $review->content }}</p>
                            </div>
                        </div>
                    @empty
                        <p>Belum ada ulasan untuk produk ini.</p>
                    @endforelse
                </div>
                <hr>
                <div class="col-md-12">
                    <h5>Tags</h5>
                    <p>
                        @forelse($product->tags ?? [] as $tag)
                            <span class="badge badge-secondary mr-1">{{ $tag->name }}</span>
                        @empty
                            -
                        @endforelse
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div id="toast-notification" class="toast-notification"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    {{-- ✅ Logika JavaScript disamakan dengan template1 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productData = @json($product);
            const isPreview = {{ $isPreview ? 'true' : 'false' }};
            const currentSubdomain = '{{ $currentSubdomain }}';
            const dynamicOptionContainer = document.getElementById('dynamic-variant-options');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const selectedVariantInput = document.getElementById('selected-variant-id');
            const stockElement = document.getElementById('variant-stock');
            const priceDisplayElement = document.getElementById('product-display-price');
            const mainImageDisplay = document.getElementById('main-image-display');
            const thumbnailContainer = document.getElementById('thumbnail-container');
            let selectedOptions = {};
            let optionNamesOrder = [];
            let currentMatchingVarian = null;
            let toastTimeout;

            function showToast(message, type = 'success') {
                const toastElement = document.getElementById('toast-notification');
                if (!toastElement) return;
                clearTimeout(toastTimeout);
                toastElement.textContent = message;
                toastElement.className = 'toast-notification';
                toastElement.classList.add(type, 'show');
                toastTimeout = setTimeout(() => toastElement.classList.remove('show'), 3000);
            }

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            }

            function getOptionValue(varian, optionName) {
                if (!varian || !varian.options_data || !Array.isArray(varian.options_data)) return null;
                const option = varian.options_data.find(opt => opt.name && opt.name.toLowerCase() === optionName
                    .toLowerCase());
                return option ? option.value : null;
            }

            function updateImages(varian) {
                const getFullUrl = (path) => path ? `{{ asset('storage') }}/${path}` :
                `{{ $product->image_url }}`;
                let primaryImageUrl = getFullUrl(varian?.image_path || productData.main_image);
                mainImageDisplay.innerHTML =
                    `<a href="${primaryImageUrl}" class="image-popup"><img src="${primaryImageUrl}" class="img-fluid" alt="${productData.name}"></a>`;
            }

            function initializeVariantSelection() {
                if (!productData.varians || productData.varians.length === 0) {
                    if (addToCartBtn) addToCartBtn.disabled = productData.stock <= 0;
                    updateImages(null);
                    return;
                }
                productData.varians.forEach(varian => {
                    if (varian.options_data && Array.isArray(varian.options_data)) {
                        varian.options_data.forEach(option => {
                            if (option.name && !optionNamesOrder.includes(option.name)) {
                                optionNamesOrder.push(option.name);
                            }
                        });
                    }
                });
                let optionsHtml = '';
                optionNamesOrder.forEach(optionName => {
                    optionsHtml += `
                        <div class="form-group">
                            <label for="select-${optionName.toLowerCase()}">${optionName}:</label>
                            <select id="select-${optionName.toLowerCase()}" class="form-control" data-option-name="${optionName}" disabled>
                                <option value="">Pilih ${optionName}</option>
                            </select>
                        </div>`;
                });
                dynamicOptionContainer.innerHTML = optionsHtml;
                optionNamesOrder.forEach((optionName, index) => {
                    const selectElement = document.getElementById(`select-${optionName.toLowerCase()}`);
                    if (selectElement) {
                        selectElement.addEventListener('change', () => {
                            selectedOptions[optionName] = selectElement.value;
                            for (let i = index + 1; i < optionNamesOrder.length; i++) {
                                selectedOptions[optionNamesOrder[i]] = '';
                                const nextSelect = document.getElementById(
                                    `select-${optionNamesOrder[i].toLowerCase()}`);
                                if (nextSelect) nextSelect.value = '';
                            }
                            updateOptionDropdowns(index + 1);
                        });
                    }
                });
                updateOptionDropdowns();
                updateImages(null);
            }

            function updateOptionDropdowns(startIndex = 0) {
                for (let i = startIndex; i < optionNamesOrder.length; i++) {
                    const currentOptionName = optionNamesOrder[i];
                    const selectElement = document.getElementById(`select-${currentOptionName.toLowerCase()}`);
                    if (!selectElement) continue;
                    const filteredVarians = productData.varians.filter(varian => {
                        for (let j = 0; j < i; j++) {
                            const prevOptionName = optionNamesOrder[j];
                            if (selectedOptions[prevOptionName] && getOptionValue(varian,
                                prevOptionName) !== selectedOptions[prevOptionName]) {
                                return false;
                            }
                        }
                        return true;
                    });
                    const availableValues = [...new Set(filteredVarians.map(v => getOptionValue(v,
                        currentOptionName)).filter(Boolean))].sort();
                    let optionsHTML = `<option value="">Pilih ${currentOptionName}</option>`;
                    availableValues.forEach(value => {
                        const hasStock = filteredVarians.some(varian => getOptionValue(varian,
                            currentOptionName) === value && varian.stock > 0);
                        optionsHTML +=
                            `<option value="${value}" ${hasStock ? '' : 'disabled'}>${value} ${hasStock ? '' : '(Habis)'}</option>`;
                    });
                    selectElement.innerHTML = optionsHTML;
                    const prevOptionSelected = (i === 0) || (selectedOptions[optionNamesOrder[i - 1]]);
                    selectElement.disabled = !prevOptionSelected || availableValues.length === 0;
                    if (selectElement.disabled) {
                        selectedOptions[currentOptionName] = '';
                    }
                }
                updateState();
            }

            function updateState() {
                const allOptionsSelected = optionNamesOrder.length === 0 || optionNamesOrder.every(name =>
                    selectedOptions[name]);
                currentMatchingVarian = null;
                if (allOptionsSelected && productData.varians.length > 0) {
                    currentMatchingVarian = productData.varians.find(varian => {
                        return optionNamesOrder.every(name => getOptionValue(varian, name) ===
                            selectedOptions[name]);
                    });
                }
                if (currentMatchingVarian && currentMatchingVarian.stock > 0) {
                    addToCartBtn.disabled = false;
                    priceDisplayElement.textContent = formatRupiah(currentMatchingVarian.price);
                    stockElement.textContent = currentMatchingVarian.stock;
                    selectedVariantInput.value = currentMatchingVarian.id;
                    updateImages(currentMatchingVarian);
                } else {
                    if (productData.varians && productData.varians.length > 0) addToCartBtn.disabled = true;
                    priceDisplayElement.textContent = formatRupiah(productData.price);
                    stockElement.textContent = productData.varians.first()?.stock ?? (productData.stock ?? 0);
                    selectedVariantInput.value = '';
                    if (!allOptionsSelected) updateImages(null);
                }
            }
            document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isPreview) return;
                const form = this;
                const submitButton = form.querySelector('#add-to-cart-btn');
                const quantity = parseInt(form.querySelector('input[name="quantity"]').value, 10);
                if (!currentMatchingVarian && productData.varians.length > 0) {
                    showToast('Silakan pilih semua opsi varian.', 'error');
                    return;
                }
                if (isNaN(quantity) || quantity < 1) {
                    showToast('Jumlah tidak valid.', 'error');
                    return;
                }
                const variantStock = currentMatchingVarian ? currentMatchingVarian.stock : productData
                .stock;
                if (variantStock < quantity) {
                    showToast(`Stok tidak mencukupi. Tersedia: ${variantStock}`, 'error');
                    return;
                }
                submitButton.disabled = true;
                submitButton.textContent = 'Menambahkan...';
                axios.post(form.action, {
                        product_id: productData.id,
                        variant_id: selectedVariantInput.value || null,
                        quantity: quantity
                    })
                    .then(res => {
                        if (res.data.success) {
                            showToast(res.data.message || 'Produk berhasil ditambahkan!', 'success');
                            if (res.data.cart_count !== undefined) {
                                const cartCountElement = document.querySelector(
                                    '.icon-shopping_cart + span');
                                if (cartCountElement) cartCountElement.textContent =
                                    `[${res.data.cart_count}]`;
                            }
                        } else {
                            throw new Error(res.data.message);
                        }
                    })
                    .catch(err => showToast(err.response?.data?.message || 'Gagal menambahkan produk.',
                        'error'))
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Tambah ke Keranjang';
                    });
            });
            $('.quantity-left-minus').on('click', function() {
                var $input = $(this).closest('.input-group').find('input');
                var count = parseInt($input.val(), 10) - 1;
                $input.val(count < 1 ? 1 : count).trigger('change');
            });
            $('.quantity-right-plus').on('click', function() {
                var $input = $(this).closest('.input-group').find('input');
                $input.val(parseInt($input.val(), 10) + 1).trigger('change');
            });
            initializeVariantSelection();
        });
    </script>
@endpush
