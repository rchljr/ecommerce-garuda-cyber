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
            font-weight: 900;
        }

        /* Style untuk tombol Add to Cart yang dinonaktifkan */
        .primary-btn:disabled {
            background-color: #b0b0b0;
            cursor: not-allowed;
        }

        /* Gaya untuk kontainer gambar */
        #main-image-display img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: contain;
            border: 1px solid #eee;
            margin-bottom: 10px;
        }

        #thumbnail-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        #thumbnail-container img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #e1e1e1;
            padding: 2px;
            transition: border-color 0.2s;
            border-radius: 4px;
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

        /* PENYESUAIAN BARU: Style untuk Varian Swatches */
        .variant-group {
            margin-bottom: 20px;
        }
        .variant-group-title {
            font-size: 15px;
            color: #111111;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .variant-swatches {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .variant-swatch {
            padding: 8px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 50px;
            cursor: pointer;
            font-size: 14px;
            color: #444;
            transition: all 0.3s;
            background-color: #fff;
        }
        .variant-swatch:hover {
            background-color: #f3f3f3;
            border-color: #111;
        }
        .variant-swatch.active {
            background-color: #111111;
            color: #ffffff;
            border-color: #111111;
        }
        .variant-swatch.disabled {
            background-color: #f3f3f3;
            color: #b0b0b0;
            cursor: not-allowed;
            text-decoration: line-through;
            border-color: #e1e1e1;
        }
        .variant-stock-info {
            font-size: 14px;
            color: #ca1515;
            margin-top: 15px;
            font-weight: 600;
            height: 20px; /* Jaga layout tetap stabil */
        }
        .product__details__cart__option {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
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
                            <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                            <a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Toko</a>
                            <span>{{ $product->name ?? 'Detail Produk' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
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

                                {{-- PENYESUAIAN BARU: Kontainer untuk Varian Swatches --}}
                                <div class="product__details__option" id="dynamic-variant-options">
                                    @if ($product->varians->isEmpty())
                                        <p class="text-muted">Produk ini tidak memiliki varian.</p>
                                    @endif
                                </div>

                                {{-- PENYESUAIAN BARU: Info Stok --}}
                                <div id="variant-stock-info" class="variant-stock-info">
                                    {{-- Diisi oleh JavaScript --}}
                                </div>
                                
                                {{-- PENYESUAIAN BARU: Tata Letak Tombol Aksi --}}
                                <div class="product__details__cart__option">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input type="text" name="quantity" value="1">
                                        </div>
                                    </div>
                                    <button type="submit" class="primary-btn" id="add-to-cart-btn" disabled>Tambah Keranjang</button>
                                </div>
                                <div class="product__details__btns__option">
                                    <a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}">
                                        <i class="fa fa-heart-o"></i> Tambah ke Wishlist
                                    </a>
                                </div>
                            </form>

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
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Ulasan ({{ $reviewCount ?? 0 }})</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                    <h6>Deskipsi</h6>
                                    <p>{!! $product->description ?? 'Deskripsi lengkap produk akan muncul di sini.' !!}</p>
                                </div>
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
                                        <li><a href="#" class="toggle-wishlist" data-product-id="{{ $relatedProduct->id }}"><i class="fa fa-heart-o"></i></a></li>
                                        <li><a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $relatedProduct->slug]) : '#' }}"><i class="fa fa-eye"></i></a></li>
                                    </ul>
                                </div>
                                <div class="product__item__text">
                                    <h6>{{ $relatedProduct->name }}</h6>
                                    <a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $relatedProduct->slug]) : '#' }}" class="add-cart">Lihat Produk</a>
                                    <div class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa {{ ($relatedProduct->rating_product ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
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
            const stockInfoElement = document.getElementById('variant-stock-info');
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
                toastElement.className = 'toast-notification';
                toastElement.classList.add(type, 'show');
                toastTimeout = setTimeout(() => {
                    toastElement.classList.remove('show');
                }, 3000);
            }

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function getOptionValue(varian, optionName) {
                if (!varian || !varian.options_data || !Array.isArray(varian.options_data)) return null;
                const option = varian.options_data.find(opt => opt.name && opt.name.toLowerCase() === optionName.toLowerCase());
                return option ? option.value : null;
            }

            function updateImages(varian) {
                const getFullUrl = (path) => path ? `{{ asset('storage') }}/${path}` : `{{ asset('images/product_image_default.png') }}`;

                let primaryImageUrl;
                const galleryImageUrls = new Set();

                // Prioritaskan gambar varian jika ada
                if (varian && varian.image_path) {
                    primaryImageUrl = getFullUrl(varian.image_path);
                    galleryImageUrls.add(primaryImageUrl);
                } else {
                    primaryImageUrl = getFullUrl(productData.main_image);
                    galleryImageUrls.add(primaryImageUrl);
                }

                // Tambahkan gambar galeri produk
                if (productData.gallery_image_paths && productData.gallery_image_paths.length > 0) {
                    productData.gallery_image_paths.forEach(path => galleryImageUrls.add(getFullUrl(path)));
                }

                const finalImages = Array.from(galleryImageUrls).filter(Boolean);

                if (finalImages.length === 0) {
                    mainImageDisplay.innerHTML = `<img src="${getFullUrl(null)}" alt="Gambar tidak tersedia">`;
                    thumbnailContainer.innerHTML = '';
                    return;
                }
                
                mainImageDisplay.innerHTML = `<img src="${finalImages[0]}" alt="${productData.name}">`;
                thumbnailContainer.innerHTML = finalImages.map((url, index) => `
                    <img src="${url}" alt="Thumbnail ${index + 1}" class="${url === primaryImageUrl ? 'active' : ''}" data-full-src="${url}">
                `).join('');
            }

            // ===================================================================
            // LOGIKA UTAMA VARIAN
            // ===================================================================
            function initializeVariantSelection() {
                if (!productData.varians || productData.varians.length === 0) {
                    addToCartBtn.disabled = productData.stock <= 0;
                    stockInfoElement.textContent = productData.stock > 0 ? `Stok: ${productData.stock}` : 'Stok Habis';
                    updateImages(null);
                    return;
                }

                // Ekstrak dan urutkan nama opsi (e.g., Ukuran, Warna)
                const optionNames = new Set();
                productData.varians.forEach(varian => {
                    if (varian.options_data) {
                        varian.options_data.forEach(opt => optionNames.add(opt.name));
                    }
                });
                optionNamesOrder = Array.from(optionNames); // Anda bisa menambahkan logika sorting di sini jika perlu

                // Buat struktur HTML untuk setiap grup opsi
                dynamicOptionContainer.innerHTML = optionNamesOrder.map(name => `
                    <div class="variant-group">
                        <h6 class="variant-group-title">${name}</h6>
                        <div class="variant-swatches" data-option-name="${name}"></div>
                    </div>
                `).join('');

                // Tambahkan event listener ke kontainer untuk menangani klik pada swatch
                dynamicOptionContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('variant-swatch') && !e.target.classList.contains('disabled')) {
                        const swatch = e.target;
                        const optionName = swatch.parentElement.dataset.optionName;
                        const value = swatch.dataset.value;

                        // Toggle pilihan
                        if (selectedOptions[optionName] === value) {
                            selectedOptions[optionName] = null;
                            swatch.classList.remove('active');
                        } else {
                            selectedOptions[optionName] = value;
                            // Hapus 'active' dari swatch lain dalam grup yang sama
                            swatch.parentElement.querySelectorAll('.variant-swatch').forEach(s => s.classList.remove('active'));
                            swatch.classList.add('active');
                        }
                        updateVariantSwatches();
                    }
                });

                updateVariantSwatches();
                updateImages(null);
            }

            function updateVariantSwatches() {
                // Iterasi melalui setiap grup opsi untuk memperbarui swatch-nya
                optionNamesOrder.forEach(optionName => {
                    const swatchesContainer = dynamicOptionContainer.querySelector(`.variant-swatches[data-option-name="${optionName}"]`);
                    if (!swatchesContainer) return;

                    // Dapatkan semua nilai unik untuk opsi saat ini
                    const allValuesForOption = [...new Set(productData.varians.map(v => getOptionValue(v, optionName)).filter(Boolean))];

                    let swatchesHTML = '';
                    allValuesForOption.forEach(value => {
                        // Cek apakah ada varian yang valid dengan kombinasi ini
                        const isPossible = productData.varians.some(varian => {
                            if (getOptionValue(varian, optionName) !== value) return false;
                            // Cek terhadap opsi lain yang sudah dipilih
                            return optionNamesOrder.every(otherName => {
                                if (otherName === optionName || !selectedOptions[otherName]) return true;
                                return getOptionValue(varian, otherName) === selectedOptions[otherName];
                            });
                        });
                        
                        const isActive = selectedOptions[optionName] === value;
                        swatchesHTML += `<div class="variant-swatch ${isActive ? 'active' : ''} ${!isPossible ? 'disabled' : ''}" data-value="${value}">${value}</div>`;
                    });
                    swatchesContainer.innerHTML = swatchesHTML;
                });
                updateState();
            }

            function updateState() {
                const allOptionsSelected = optionNamesOrder.every(name => selectedOptions[name]);
                
                currentMatchingVarian = null;
                if (allOptionsSelected) {
                    currentMatchingVarian = productData.varians.find(varian => 
                        optionNamesOrder.every(name => getOptionValue(varian, name) === selectedOptions[name])
                    );
                }

                if (currentMatchingVarian) {
                    const hasStock = currentMatchingVarian.stock > 0;
                    addToCartBtn.disabled = !hasStock;
                    stockInfoElement.textContent = hasStock ? `Stok: ${currentMatchingVarian.stock}` : 'Stok Habis';
                    priceDisplayElement.textContent = formatRupiah(currentMatchingVarian.price);
                    if(skuElement) skuElement.textContent = currentMatchingVarian.sku || 'N/A';
                    selectedVariantInput.value = currentMatchingVarian.id;
                    updateImages(currentMatchingVarian);
                } else {
                    addToCartBtn.disabled = true;
                    stockInfoElement.textContent = 'Pilih varian untuk melihat stok';
                    priceDisplayElement.textContent = formatRupiah(productData.price);
                    if(skuElement) skuElement.textContent = productData.sku || 'N/A';
                    selectedVariantInput.value = '';
                    // Jangan reset gambar jika hanya sebagian varian terpilih
                    if (Object.values(selectedOptions).every(v => !v)) {
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

                if (!currentMatchingVarian && productData.varians.length > 0) {
                    showToast('Silakan pilih semua opsi varian.', 'error');
                    return;
                }
                if (isNaN(quantity) || quantity < 1) {
                    showToast('Jumlah tidak valid.', 'error');
                    return;
                }
                
                const stock = currentMatchingVarian ? currentMatchingVarian.stock : productData.stock;
                if (stock < quantity) {
                    showToast(`Stok tidak mencukupi. Tersedia: ${stock}`, 'error');
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = 'MENAMBAHKAN...';

                axios.post(form.action, {
                    product_id: productData.id,
                    variant_id: currentMatchingVarian ? currentMatchingVarian.id : null,
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
                    const icon = wishlistBtn.querySelector('i');

                    axios.post(`/tenant/${currentSubdomain}/wishlist/toggle`, { product_id: productId })
                    .then(res => {
                        if (res.data.success) {
                            const wasAdded = res.data.action === 'added';
                            showToast(wasAdded ? 'Ditambahkan ke wishlist!' : 'Dihapus dari wishlist.', 'success');
                            document.querySelectorAll(`.toggle-wishlist[data-product-id="${productId}"]`).forEach(btn => {
                                btn.classList.toggle('active', wasAdded);
                                btn.querySelector('i').className = wasAdded ? 'fa fa-heart' : 'fa fa-heart-o';
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
                    $button.parent().find('input').val(newVal).trigger('change');
                });
            }

            // Panggil inisialisasi varian setelah DOM siap
            initializeVariantSelection();
        });
    </script>
@endpush
