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
        .product__details__btns__option .toggle-wishlist.active i,
        .product__hover .toggle-wishlist.active i {
            color: #e53636;
            font-weight: 900; /* Make it bold */
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
        
        /* Style untuk review */
        .review-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .review-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .review-header h6 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        .review-rating .fa {
            color: #f7941d;
        }
        .review-content {
            color: #666;
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    <section class="shop-details">
        <div class="product_details_pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product_details_breadcrumb">
                            <a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                            <a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Toko</a>
                            <span>{{ $product->name ?? 'Detail Produk' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="product_detailspic_container">
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
                        <div class="product_details_text">
                            <h4>{{ $product->name ?? 'Nama Produk' }}</h4>
                            {{-- PERUBAHAN: Rating dan jumlah ulasan dinamis --}}
                            <div class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ ($averageRating ?? 0) >= $i ? 'fa-star' : (($averageRating ?? 0) > $i - 1 ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                                <span> - {{ $reviewCount ?? 0 }} Ulasan</span>
                            </div>
                            <h3 id="product-display-price">{{ format_rupiah($product->price) }}</h3>
                            <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                            <form id="add-to-cart-form"
                                action="{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                                <div class="product_details_option" id="dynamic-variant-options">
                                    @if ($product->varians->isEmpty())
                                        <p class="text-muted">Produk ini tidak memiliki varian.</p>
                                    @endif
                                </div>

                                <div class="product_detailscart_option">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input type="text" name="quantity" value="1">
                                        </div>
                                    </div>
                                    <button type="submit" class="primary-btn" id="add-to-cart-btn"
                                        {{ $product->varians->isEmpty() ? 'disabled' : '' }}>Tambah Keranjang</button>
                                </div>
                            </form>

                            <div class="product_detailsbtns_option">
                                <a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i
                                        class="fa fa-heart"></i> Tambah ke Wishlist</a>
                            </div>

                            <div class="product__details__last__option">
                                <h5><span>Garansi Pembelian</span></h5>
                                <img src="{{ asset('images/Frame 8.png') }}" alt="Payment Methods" height="40">
                                <ul>
                                    <li><span>SKU:</span> <span id="variant-sku">{{ $product->sku ?? 'N/A' }}</span></li>
                                    <li><span>Kategori:</span> {{ $product->subCategory->name ?? 'Uncategorized' }}</li>
                                    <li><span>Tag:</span>
                                        @forelse($product->tags as $tag)
                                            {{ $tag->name }}{{ !$loop->last ? ', ' : '' }}
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tabs-1"
                                        role="tab">Description</a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Specification</a>
                                </li> --}}
                                {{-- PERUBAHAN: Jumlah ulasan di tab dinamis --}}
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Ulasan ({{ $reviewCount ?? 0 }})</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                    <h6>Deskipsi</h6>
                                    <p>{!! $product->description ?? 'Deskripsi lengkap produk akan muncul di sini.' !!}</p>
                                </div>
                                {{-- <div class="tab-pane" id="tabs-2" role="tabpanel">
                                    <h6>Specification</h6>
                                    <p>{!! $product->specification ?? 'Spesifikasi produk akan muncul di sini.' !!}</p>
                                </div> --}}
                                {{-- PERUBAHAN: Konten tab ulasan dinamis --}}
                                <div class="tab-pane" id="tabs-3" role="tabpanel">
                                    <h6>Ulasan Pelanggan ({{ $reviewCount ?? 0 }})</h6>
                                    @forelse ($reviews as $review)
                                        <div class="review-item">
                                            <div class="review-header">
                                                <h6>{{ $review->name }}</h6>
                                                <div class="review-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa {{ $review->rating >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p class="review-content">{{ $review->content }}</p>
                                        </div>
                                    @empty
                                        <p>Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!</p>
                                    @endforelse
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
                                        class="add-cart">Lihat Produk</a>
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productData = @json($product);
            
            const isPreview = {{ $isPreview ? 'true' : 'false' }};
            const currentSubdomain = '{{ $currentSubdomain }}';

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
            let toastTimeout;

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

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function getOptionValue(varian, optionName) {
                if (!varian || !varian.options_data || !Array.isArray(varian.options_data)) {
                    return null;
                }
                const option = varian.options_data.find(opt => opt.name && opt.name.toLowerCase() === optionName.toLowerCase());
                return option ? option.value : null;
            }
            
            function updateImages(varian) {
                const getFullUrl = (path) => path ? `{{ asset('storage') }}/${path}` : null;

                let primaryImageUrl = getFullUrl(varian?.image_path || productData.main_image);
                let galleryImageUrls = [];
                
                if (productData.gallery_image_paths && productData.gallery_image_paths.length > 0) {
                    galleryImageUrls = productData.gallery_image_paths.map(path => getFullUrl(path));
                } else if (productData.gallery && productData.gallery.length > 0) {
                    galleryImageUrls = productData.gallery.map(img => img.image_url); // Fallback
                }

                const displayImages = new Set();
                if (primaryImageUrl) displayImages.add(primaryImageUrl);
                galleryImageUrls.forEach(url => {
                    if (url) displayImages.add(url);
                });

                const finalImages = Array.from(displayImages);

                if (finalImages.length === 0) {
                    mainImageDisplay.innerHTML = '<p>Gambar tidak tersedia.</p>';
                    thumbnailContainer.innerHTML = '';
                    return;
                }

                mainImageDisplay.innerHTML = `<img src="${finalImages[0]}" alt="${productData.name}">`;
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

                optionNamesOrder.sort((a, b) => {
                    const order = ['size', 'ukuran', 'color', 'warna'];
                    let indexA = order.indexOf(a.toLowerCase());
                    let indexB = order.indexOf(b.toLowerCase());
                    if (indexA === -1) indexA = 999;
                    if (indexB === -1) indexB = 999;
                    return indexA - indexB;
                });

                let optionsHtml = '';
                optionNamesOrder.forEach(optionName => {
                    optionsHtml += `
                        <div class="option-select-group">
                            <label for="select-${optionName.toLowerCase()}" class="d-block">${optionName}:</label>
                            <select id="select-${optionName.toLowerCase()}" class="variant-option-select" required data-option-name="${optionName}" disabled>
                                <option value="">Pilih ${optionName}</option>
                            </select>
                        </div>`;
                });
                dynamicOptionContainer.innerHTML = optionsHtml;

                optionNamesOrder.forEach((optionName, index) => {
                    const selectElement = document.getElementById(select-${optionName.toLowerCase()});
                    if (selectElement) {
                        selectElement.addEventListener('change', () => {
                            selectedOptions[optionName] = selectElement.value;
                            for (let i = index + 1; i < optionNamesOrder.length; i++) {
                                selectedOptions[optionNamesOrder[i]] = '';
                                const nextSelect = document.getElementById(select-${optionNamesOrder[i].toLowerCase()});
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
                    const selectElement = document.getElementById(select-${currentOptionName.toLowerCase()});
                    if (!selectElement) continue;

                    const filteredVarians = productData.varians.filter(varian => {
                        for (let j = 0; j < i; j++) {
                            const prevOptionName = optionNamesOrder[j];
                            if (selectedOptions[prevOptionName] && getOptionValue(varian, prevOptionName) !== selectedOptions[prevOptionName]) {
                                return false;
                            }
                        }
                        return true;
                    });

                    const availableValues = [...new Set(filteredVarians.map(v => getOptionValue(v, currentOptionName)).filter(Boolean))].sort();
                    
                    let optionsHTML = <option value="">Pilih ${currentOptionName}</option>;
                    availableValues.forEach(value => {
                        const hasStock = filteredVarians.some(varian => getOptionValue(varian, currentOptionName) === value && varian.stock > 0);
                        optionsHTML += <option value="${value}" ${hasStock ? '' : 'disabled'}>${value} ${hasStock ? '' : '(Habis)'}</option>;
                    });
                    selectElement.innerHTML = optionsHTML;

                    const prevOptionSelected = i === 0 || selectedOptions[optionNamesOrder[i - 1]];
                    selectElement.disabled = !prevOptionSelected || availableValues.length === 0;

                    if (selectElement.disabled) {
                        selectedOptions[currentOptionName] = '';
                    }
                }
                updateState();
            }

            function updateState() {
                const allOptionsSelected = optionNamesOrder.length === 0 || optionNamesOrder.every(name => selectedOptions[name]);
                
                currentMatchingVarian = null;
                if (allOptionsSelected && productData.varians.length > 0) {
                    currentMatchingVarian = productData.varians.find(varian => {
                        return optionNamesOrder.every(name => getOptionValue(varian, name) === selectedOptions[name]);
                    });
                }

                if (currentMatchingVarian && currentMatchingVarian.stock > 0) {
                    addToCartBtn.disabled = false;
                    priceDisplayElement.textContent = formatRupiah(currentMatchingVarian.price);
                    if(skuElement) skuElement.textContent = currentMatchingVarian.sku || 'N/A';
                    selectedVariantInput.value = currentMatchingVarian.id;
                    updateImages(currentMatchingVarian);
                } else {
                    addToCartBtn.disabled = true;
                    priceDisplayElement.textContent = formatRupiah(productData.price);
                    if(skuElement) skuElement.textContent = productData.sku || 'N/A';
                    selectedVariantInput.value = '';
                    if (!allOptionsSelected) {
                        updateImages(null);
                    }
                }
            }

            // ===================================================================
            // HANDLER UNTUK AKSI (CART, WISHLIST, GAMBAR)
            // ===================================================================

            document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isPreview) return;

                const form = this;
                const submitButton = form.querySelector('#add-to-cart-btn');
                const quantity = parseInt(form.querySelector('input[name="quantity"]').value, 10);

                if (!currentMatchingVarian) {
                    showToast('Silakan pilih semua opsi varian.', 'error');
                    return;
                }
                if (isNaN(quantity) || quantity < 1) {
                    showToast('Jumlah tidak valid.', 'error');
                    return;
                }
                if (currentMatchingVarian.stock < quantity) {
                    showToast(Stok tidak mencukupi. Tersedia: ${currentMatchingVarian.stock}, 'error');
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = 'MENAMBAHKAN...';

                axios.post(form.action, {
                    product_id: productData.id,
                    variant_id: currentMatchingVarian.id,
                    quantity: quantity
                })
                .then(res => {
                    if (res.data.success) {
                        showToast(res.data.message || 'Produk berhasil ditambahkan!', 'success');
                        if (res.data.cart_count !== undefined) {
                            const cartCountElement = document.getElementById('cart-count');
                            if (cartCountElement) cartCountElement.textContent = res.data.cart_count;
                        }
                    } else {
                        throw new Error(res.data.message);
                    }
                })
                .catch(err => {
                    showToast(err.response?.data?.message || 'Gagal menambahkan produk.', 'error');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Tambah Keranjang';
                });
            });

            document.body.addEventListener('click', function(e) {
                const wishlistBtn = e.target.closest('.toggle-wishlist');
                if (wishlistBtn) {
                    e.preventDefault();
                    if (isPreview) return;
                    
                    const productId = wishlistBtn.dataset.productId;
                    axios.post(`/tenant/${currentSubdomain}/wishlist/toggle`, { product_id: productId })
                    .then(res => {
                        if (res.data.success) {
                            const wasAdded = res.data.action === 'added';
                            showToast(wasAdded ? 'Ditambahkan ke wishlist!' : 'Dihapus dari wishlist.', 'success');
                            document.querySelectorAll(`.toggle-wishlist[data-product-id="${productId}"]`).forEach(btn => {
                                btn.classList.toggle('active', wasAdded);
                            });
                        }
                    })
                    .catch(err => {
                        if (err.response?.status === 401) {
                            showToast('Silakan login untuk memakai wishlist.', 'error');
                        } else {
                            showToast(err.response?.data?.message || 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });

            thumbnailContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'IMG') {
                    mainImageDisplay.querySelector('img').src = e.target.dataset.fullSrc;
                    thumbnailContainer.querySelectorAll('img').forEach(img => img.classList.remove('active'));
                    e.target.classList.add('active');
                }
            });

            // Inisialisasi PRO-QTY (Quantity Selector)
            var proQty = $('.pro-qty');
            if (proQty.length) {
                proQty.prepend('<span class="fa fa-angle-left dec qtybtn"></span>');
                proQty.append('<span class="fa fa-angle-right inc qtybtn"></span>');
                proQty.on('click', '.qtybtn', function() {
                    var $button = $(this);
                    var oldValue = $button.parent().find('input').val();
                    var newVal = $button.hasClass('inc') ? parseFloat(oldValue) + 1 : (oldValue > 1 ? parseFloat(oldValue) - 1 : 1);
                    $button.parent().find('input').val(newVal);
                });
            }

            // Panggil inisialisasi varian setelah DOM siap
            initializeVariantSelection();
        });
    </script>
@endpush
