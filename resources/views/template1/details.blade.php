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

        /* Style untuk ikon wishlist yang aktif */
        .product__details__btns__option .toggle-wishlist.active i {
            color: #e53636;
        }

        /* Style untuk tombol Add to Cart yang dinonaktifkan */
        .primary-btn:disabled {
            background-color: #b0b0b0;
            cursor: not-allowed;
        }

        /* Style tambahan untuk dropdown varian */
        .product__details__option .option-select-group {
            margin-bottom: 15px;
        }

        .product__details__option .option-select-group select {
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

        .product__details__option .option-select-group select:disabled {
            background-color: #f3f3f3;
            color: #b0b0b0;
            cursor: not-allowed;
        }

        /* Gaya untuk kontainer gambar */
        #main-image-display img {
            max-width: 100%;
            height: auto;
            border: 1px solid #eee;
            margin-bottom: 10px;
        }

        #thumbnail-container img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 5px;
            cursor: pointer;
            border: 2px solid #ccc;
            padding: 2px;
            transition: border-color 0.2s;
        }

        #thumbnail-container img.active,
        #thumbnail-container img:hover {
            border-color: #ca1515;
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

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
                    {{-- PERBAIKAN: Struktur kolom diubah agar gambar dan deskripsi sejajar dengan benar di layar besar --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="product__details__pic__container">
                            {{-- Kontainer Gambar Utama --}}
                            <div id="main-image-display" class="mb-3">
                                {{-- Diisi oleh JavaScript --}}
                            </div>
                            {{-- Kontainer Thumbnail --}}
                            <div id="thumbnail-container">
                                {{-- Diisi oleh JavaScript --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="product__details__text">
                            <h4>{{ $product->name ?? 'Nama Produk' }}</h4>
                            <div class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fa {{ ($product->rating ?? 0) >= $i ? 'fa-star' : (($product->rating ?? 0) > $i - 1 ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                                <span> - {{ $product->reviews_count ?? 0 }} Reviews</span>
                            </div>
                            <h3 id="product-display-price">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
                            <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                            <form id="add-to-cart-form"
                                action="{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" id="selected-variant-id" name="varian_id" value="">

                                <div class="product__details__option" id="dynamic-variant-options">
                                    {{-- Opsi varian dinamis akan di-generate oleh JavaScript di sini --}}
                                    @if ($product->varians->isEmpty())
                                        <p class="text-muted">Produk ini tidak memiliki varian.</p>
                                    @endif
                                </div>

                                <div class="product__details__cart__option">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input type="text" name="quantity" value="1">
                                        </div>
                                    </div>
                                    {{-- PERBAIKAN: Tombol add-to-cart di-disable jika produk tidak punya varian sama sekali --}}
                                    <button type="submit" class="primary-btn" id="add-to-cart-btn"
                                        {{ $product->varians->isEmpty() ? 'disabled' : '' }}>Tambah Keranjang</button>
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
                                    <li><span>SKU:</span> <span id="variant-sku">{{ $product->sku ?? 'N/A' }}</span></li>
                                    <li><span>Kategori:</span> {{ $product->subCategory->name ?? 'Uncategorized' }}</li>
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
            </div>
        </div>
        <div class="product__details__content">
            <div class="container">
                {{-- Bagian Deskripsi Produk Lengkap dan Ulasan --}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tabs-1"
                                        role="tab">Description</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Specification</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Reviews (
                                        {{ $product->reviews_count ?? 0 }} )</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                    <h6>Description</h6>
                                    <p>{!! $product->description ?? 'Deskripsi lengkap produk akan muncul di sini.' !!}</p>
                                </div>
                                <div class="tab-pane" id="tabs-2" role="tabpanel">
                                    <h6>Specification</h6>
                                    <p>{!! $product->specification ?? 'Spesifikasi produk akan muncul di sini.' !!}</p>
                                </div>
                                <div class="tab-pane" id="tabs-3" role="tabpanel">
                                    <h6>Reviews ({{ $product->reviews_count ?? 0 }})</h6>
                                    {{-- Anda bisa menambahkan logika untuk menampilkan ulasan di sini --}}
                                    <p>...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Bagian Produk Terkait (Related Products) --}}
    @if (isset($relatedProducts) && $relatedProducts->count() > 0)
        <section class="related spad">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="related-title">Related Product</h3>
                    </div>
                </div>
                <div class="row">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="product__item">
                                <div class="product__item__pic set-bg" data-setbg="{{ $relatedProduct->image_url }}">
                                    <ul class="product__hover">
                                        <li><a href="#" class="toggle-wishlist"
                                                data-product-id="{{ $relatedProduct->id }}"><i
                                                    class="fa fa-heart-o"></i></a></li>
                                        <li><a
                                                href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $relatedProduct->slug]) : '#' }}"><i
                                                    class="fa fa-eye"></i></a></li>
                                    </ul>
                                </div>
                                <div class="product__item__text">
                                    <h6>{{ $relatedProduct->name }}</h6>
                                    <a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $relatedProduct->slug]) : '#' }}"
                                        class="add-cart">View Product</a>
                                    <div class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fa {{ ($relatedProduct->rating_product ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                        @endfor
                                    </div>
                                    <h5>{{ format_rupiah($relatedProduct->price) }}</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <div id="toast-notification" class="toast-notification"></div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data produk lengkap, termasuk varian dan options_data
            const productData = @json($product);

            const dynamicOptionContainer = document.getElementById('dynamic-variant-options');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const selectedVariantInput = document.getElementById('selected-variant-id');
            const skuElement = document.getElementById('variant-sku');
            const priceDisplayElement = document.getElementById('product-display-price');
            const mainImageDisplay = document.getElementById('main-image-display');
            const thumbnailContainer = document.getElementById('thumbnail-container');

            let selectedOptions = {};
            let optionNamesOrder = [];
            let currentMatchingVarian = null;
            let toastTimeout; // PERBAIKAN: Deklarasi variabel timeout untuk toast

            // ===================================================================
            // FUNGSI BANTUAN (HELPERS)
            // ===================================================================

            function showToast(message, type = 'success') {
                const toastElement = document.getElementById('toast-notification');
                if (!toastElement) return;

                clearTimeout(toastTimeout);
                toastElement.textContent = message;
                toastElement.className = 'toast-notification'; // Reset class
                toastElement.classList.add(type, 'show');
                toastTimeout = setTimeout(() => {
                    toastElement.classList.remove('show');
                }, 3000);
            }

            function getOptionValue(varian, optionName) {
                if (!varian || !varian.options_data || !Array.isArray(varian.options_data)) {
                    return null;
                }
                const option = varian.options_data.find(opt => opt.name && opt.name.toLowerCase() === optionName
                    .toLowerCase());
                return option ? option.value : null;
            }

            // PERBAIKAN: Fungsi ini disederhanakan dan diperbaiki
            function updateImages(varian) {
                // Helper untuk membuat URL lengkap dari path storage
                const getFullUrl = (path) => path ? `{{ asset('storage') }}/${path}` : null;

                // Tentukan gambar utama dan galeri
                let primaryImageUrl = null;
                let galleryImageUrls = [];

                // Prioritas 1: Gambar dari varian yang dipilih
                if (varian && varian.image_url) {
                    primaryImageUrl = varian.image_url;
                }
                // Prioritas 2: Gambar utama produk
                else if (productData.image_url) {
                    primaryImageUrl = productData.image_url;
                }

                // Ambil galeri dari produk
                if (productData.gallery && productData.gallery.length > 0) {
                    galleryImageUrls = productData.gallery.map(img => img.image_url);
                }

                // Gabungkan semua gambar unik
                const displayImages = new Set();
                if (primaryImageUrl) displayImages.add(primaryImageUrl);
                galleryImageUrls.forEach(url => displayImages.add(url));

                const finalImages = Array.from(displayImages);

                // Update DOM
                if (finalImages.length === 0) {
                    mainImageDisplay.innerHTML = '<p>Gambar tidak tersedia.</p>';
                    thumbnailContainer.innerHTML = '';
                    return;
                }

                // Set gambar utama dengan gambar pertama dari daftar
                mainImageDisplay.innerHTML = `<img src="${finalImages[0]}" alt="${productData.name}">`;

                // Buat thumbnail
                thumbnailContainer.innerHTML = finalImages.map((url, index) => `
                    <img src="${url}" alt="Thumbnail ${index + 1}" class="${index === 0 ? 'active' : ''}" data-full-src="${url}">
                `).join('');
            }


            // ===================================================================
            // LOGIKA UTAMA VARIAN
            // ===================================================================

            function initializeVariantSelection() {
                if (!productData.varians || productData.varians.length === 0) {
                    addToCartBtn.disabled = true;
                    updateImages(null); // Tampilkan gambar default produk
                    dynamicOptionContainer.innerHTML =
                        '<p class="text-muted">Produk ini tidak memiliki varian.</p>';
                    return;
                }

                // Kumpulkan semua nama opsi unik (e.g., 'Warna', 'Ukuran')
                productData.varians.forEach(varian => {
                    if (varian.options_data && Array.isArray(varian.options_data)) {
                        varian.options_data.forEach(option => {
                            if (option.name && !optionNamesOrder.includes(option.name)) {
                                optionNamesOrder.push(option.name);
                            }
                        });
                    }
                });

                // Urutkan opsi (opsional, untuk konsistensi UI)
                optionNamesOrder.sort((a, b) => {
                    const order = ['size', 'ukuran', 'color', 'warna'];
                    let indexA = order.indexOf(a.toLowerCase());
                    let indexB = order.indexOf(b.toLowerCase());
                    if (indexA === -1) indexA = 999;
                    if (indexB === -1) indexB = 999;
                    return indexA - indexB;
                });

                // Buat HTML untuk dropdown
                let optionsHtml = '';
                optionNamesOrder.forEach(optionName => {
                    optionsHtml += `
                        <div class="option-select-group">
                            <label for="select-${optionName.toLowerCase()}" class="d-block">${optionName}:</label>
                            <select id="select-${optionName.toLowerCase()}" class="variant-option-select" required data-option-name="${optionName}" disabled>
                                <option value="">Pilih ${optionName}</option>
                            </select>
                        </div>
                    `;
                });
                dynamicOptionContainer.innerHTML = optionsHtml;

                // Tambahkan event listener untuk setiap dropdown
                optionNamesOrder.forEach((optionName, index) => {
                    const selectElement = dynamicOptionContainer.querySelector(
                        `#select-${optionName.toLowerCase()}`);
                    if (selectElement) {
                        selectElement.addEventListener('change', () => {
                            selectedOptions[optionName] = selectElement.value;

                            // Reset dropdown berikutnya
                            for (let i = index + 1; i < optionNamesOrder.length; i++) {
                                selectedOptions[optionNamesOrder[i]] = '';
                                const nextSelect = dynamicOptionContainer.querySelector(
                                    `#select-${optionNamesOrder[i].toLowerCase()}`);
                                if (nextSelect) nextSelect.value = '';
                            }
                            updateOptionDropdowns(index + 1);
                            updateState();
                        });
                    }
                });

                updateOptionDropdowns(); // Isi dropdown pertama
                updateState();
                updateImages(null); // Tampilkan gambar awal produk
            }

            function updateOptionDropdowns(startIndex = 0) {
                if (!productData.varians) return;

                for (let i = startIndex; i < optionNamesOrder.length; i++) {
                    const currentOptionName = optionNamesOrder[i];
                    const selectElement = dynamicOptionContainer.querySelector(
                        `#select-${currentOptionName.toLowerCase()}`);
                    if (!selectElement) continue;

                    // Filter varian yang cocok dengan pilihan sebelumnya
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

                    // Dapatkan nilai yang tersedia untuk dropdown saat ini
                    const availableValues = [...new Set(filteredVarians.map(v => getOptionValue(v,
                        currentOptionName)).filter(Boolean))];
                    availableValues.sort();

                    let optionsHTML = `<option value="">Pilih ${currentOptionName}</option>`;
                    availableValues.forEach(value => {
                        // Cek apakah ada stok untuk kombinasi ini
                        const hasStock = filteredVarians.some(varian =>
                            getOptionValue(varian, currentOptionName) === value && varian
                            .stock > 0
                        );
                        optionsHTML +=
                            `<option value="${value}" ${hasStock ? '' : 'disabled'}>${value} ${hasStock ? '' : '(Stok Habis)'}</option>`;
                    });

                    selectElement.innerHTML = optionsHTML;

                    // Aktifkan dropdown jika ada pilihan sebelumnya dan ada opsi tersedia
                    const prevOptionSelected = i === 0 ? true : selectedOptions[optionNamesOrder[i - 1]];
                    selectElement.disabled = !prevOptionSelected || availableValues.length === 0;

                    if (selectElement.disabled) {
                        selectElement.value = '';
                        selectedOptions[currentOptionName] = '';
                    }
                }
                updateState();
            }

            function updateState() {
                const allOptionsSelected = optionNamesOrder.every(name => selectedOptions[name]);

                currentMatchingVarian = null;
                if (allOptionsSelected && productData.varians) {
                    currentMatchingVarian = productData.varians.find(varian => {
                        return optionNamesOrder.every(name => getOptionValue(varian, name) ===
                            selectedOptions[name]);
                    });
                }

                if (currentMatchingVarian && currentMatchingVarian.stock > 0) {
                    addToCartBtn.disabled = false;
                    priceDisplayElement.textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(currentMatchingVarian.price);
                    skuElement.textContent = currentMatchingVarian.sku || 'N/A';
                    selectedVariantInput.value = currentMatchingVarian.id;
                    updateImages(currentMatchingVarian); // PERBAIKAN: Panggil fungsi yang benar
                } else {
                    addToCartBtn.disabled = true;
                    priceDisplayElement.textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(productData.price);
                    skuElement.textContent = productData.sku || 'N/A';
                    selectedVariantInput.value = '';
                    if (!allOptionsSelected) {
                        updateImages(null); // PERBAIKAN: Kembali ke gambar default jika pilihan belum lengkap
                    }
                }
            }


            // ===================================================================
            // HANDLER UNTUK AKSI (CART, WISHLIST, GAMBAR)
            // ===================================================================

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
                e.preventDefault();

                if (!currentMatchingVarian || optionNamesOrder.some(name => !selectedOptions[name])) {
                    showToast('Silakan pilih semua opsi varian terlebih dahulu.', 'error');
                    return;
                }

                const form = this;
                const submitButton = form.querySelector('#add-to-cart-btn');
                const quantity = parseInt(form.querySelector('input[name="quantity"]').value, 10);

                if (isNaN(quantity) || quantity < 1) {
                    showToast('Jumlah tidak valid.', 'error');
                    return;
                }

                if (currentMatchingVarian.stock < quantity) {
                    showToast(`Stok tidak mencukupi. Tersedia: ${currentMatchingVarian.stock}`, 'error');
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = 'MENAMBAHKAN...';

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            varian_id: currentMatchingVarian.id,
                            quantity: quantity
                        })
                    })
                    .then(res => res.json().then(data => ({
                        status: res.status,
                        body: data
                    })))
                    .then(obj => {
                        if (obj.status >= 400) {
                            throw new Error(obj.body.message || 'Gagal menambahkan produk.');
                        }
                        showToast(obj.body.message || 'Produk berhasil ditambahkan!', 'success');
                        // Opsional: perbarui ikon keranjang
                        if (obj.body.cart_count !== undefined) {
                            const cartCountElement = document.getElementById('cart-count');
                            if (cartCountElement) cartCountElement.textContent = obj.body.cart_count;
                        }
                    })
                    .catch(err => {
                        showToast(err.message, 'error');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Tambah Keranjang';
                    });
            });

            // PENAMBAHAN: Event listener untuk klik pada thumbnail
            thumbnailContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'IMG') {
                    const mainImg = mainImageDisplay.querySelector('img');
                    if (mainImg) {
                        mainImg.src = e.target.dataset.fullSrc;
                    }
                    // Update class 'active'
                    thumbnailContainer.querySelectorAll('img').forEach(img => img.classList.remove('active'));
                    e.target.classList.add('active');
                }
            });


            // Inisialisasi PRO-QTY (Quantity Selector)
            var proQty = $('.pro-qty');
            if (proQty.length) {
                proQty.prepend('<span class="fa fa-angle-down dec qtybtn"></span>');
                proQty.append('<span class="fa fa-angle-up inc qtybtn"></span>');
                proQty.off('click', '.qtybtn').on('click', '.qtybtn', function() {
                    var $button = $(this);
                    var oldValue = $button.parent().find('input').val();
                    var newVal;
                    if ($button.hasClass('inc')) {
                        newVal = parseFloat(oldValue) + 1;
                    } else {
                        newVal = (oldValue > 1) ? parseFloat(oldValue) - 1 : 1;
                    }
                    $button.parent().find('input').val(newVal);
                });
            }

            // Panggil inisialisasi varian setelah DOM siap
            initializeVariantSelection();
        });
    </script>
@endpush