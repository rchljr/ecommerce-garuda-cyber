@extends('template1.layouts.template')

@section('title', 'Toko - ' . optional($currentShop)->shop_name)

@push('styles')
    {{-- CSS Khusus untuk Notifikasi Toast, Wishlist, dan Modal Varian --}}
    <style>
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 10001; /* Di atas modal overlay */
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
            font-family: 'Nunito Sans', sans-serif;
        }
        .toast-notification.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .toast-notification.success { background-color: #28a745; }
        .toast-notification.error { background-color: #dc3545; }
        .product__hover .toggle-wishlist.active img { filter: invert(25%) sepia(100%) saturate(5000%) hue-rotate(330deg); }
        .product__item { transition: all 0.3s ease; border: 1px solid #f2f2f2; border-radius: 5px; overflow: hidden; }
        .product__item:hover { box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08); transform: translateY(-5px); }
        .shop__sidebar { background-color: #f9f9f9; padding: 30px; border-radius: 5px; }
        .shop__product__option { padding: 15px 20px; border: 1px solid #f2f2f2; border-radius: 5px; background-color: #ffffff; margin-bottom: 30px; }
        .sidebar__categories ul li a.active { color: #111111; font-weight: 700; }

        /* PERBAIKAN: Memberikan gaya pada tombol Add to Cart agar sesuai template */
        .product__item__text .add-cart {
            font-size: 14px;
            color: #111111;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 1px solid #111111;
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .product__item__text .add-cart:hover {
            background: #111111;
            color: #ffffff;
        }

        /* Gaya untuk Modal Varian */
        .variant-modal {
            display: none; /* Sembunyikan secara default */
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            -webkit-animation-name: fadeIn;
            animation-name: fadeIn;
            -webkit-animation-duration: 0.4s;
            animation-duration: 0.4s
        }
        .variant-modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fefefe;
            padding: 30px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: slideIn;
            animation-name: slideIn;
            -webkit-animation-duration: 0.4s;
            animation-duration: 0.4s
        }
        .variant-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            line-height: 1;
        }
        .variant-modal-close:hover,
        .variant-modal-close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .variant-product-info {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .variant-product-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 20px;
        }
        .variant-product-details h6 { font-size: 18px; color: #111; margin-bottom: 5px; }
        .variant-product-details p { font-size: 16px; color: #e53637; font-weight: 700; margin-bottom: 0; }
        .variant-selection .form-group { margin-bottom: 15px; }
        .variant-selection label { display: block; margin-bottom: 5px; font-weight: 600; }
        .variant-selection select, .variant-selection input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }

        @-webkit-keyframes slideIn { from {top: -300px; opacity: 0} to {top: 50%; opacity: 1} }
        @keyframes slideIn { from {top: -300px; opacity: 0} to {top: 50%; opacity: 1} }
        @-webkit-keyframes fadeIn { from {opacity: 0} to {opacity: 1} }
        @keyframes fadeIn { from {opacity: 0} to {opacity: 1} }
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
                        <h4>Shop</h4>
                        <div class="breadcrumb__links">
                            @if($isPreview)
                                <a>Home</a>
                            @else
                                <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a>
                            @endif
                            <span>/</span>
                            <span>Shop</span>
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
            <form id="filter-sort-form" method="GET" action="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">
                <div class="row">
                    {{-- Sidebar Filter --}}
                    <div class="col-lg-3 col-md-3">
                        <div class="shop__sidebar">
                            <div class="sidebar__categories mb-4">
                                <div class="section-title">
                                    <h4>Categories</h4>
                                </div>
                                <ul>
                                    <li><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}" class="{{ !request('category') ? 'active' : '' }}">All Categories</a></li>
                                    @forelse ($categories as $subCategory)
                                        <li><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain, 'category' => $subCategory->slug]) : '#' }}"
                                               class="{{ request('category') == $subCategory->slug ? 'active' : '' }}">{{ $subCategory->name }}</a>
                                        </li>
                                    @empty
                                        <li><a href="#">Tidak ada kategori</a></li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="sidebar__filter">
                                <div class="section-title">
                                    <h4>Shop by price</h4>
                                </div>
                                <div class="filter-range-wrap">
                                    <div class="price-range ui-slider ui-corner-all ui-widget ui-widget-content"
                                         data-min="10000" data-max="500000">
                                    </div>
                                    <div class="range-slider">
                                        <div class="price-input">
                                            <p>Price:</p>
                                            <input type="text" id="minamount" name="min_price">
                                            <input type="text" id="maxamount" name="max_price">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="primary-btn mt-3" style="width: 100%; border: none;" {{ $isPreview ? 'disabled' : '' }}>Filter</button>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Produk --}}
                    <div class="col-lg-9 col-md-9">
                        <div class="shop__product__option">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <p>Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari
                                        {{ $products->total() }} hasil
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="select__option">
                                        <select name="sort" id="sort-by" class="nice-select" {{ $isPreview ? 'disabled' : '' }}>
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                                Urutkan: Terbaru</option>
                                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                                Harga: Rendah ke Tinggi</option>
                                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @forelse ($products as $product)
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item mb-4">
                                        <div class="product__item__pic set-bg" data-setbg="{{ asset('storage/' . $product->main_image) }}">
                                            @if ($product->is_new ?? false)
                                                <span class="label new">New</span>
                                            @elseif (($product->discount_percentage ?? 0) > 0)
                                                <span class="label sale">Sale</span>
                                            @endif
                                            <ul class="product__hover">
                                                <li><a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><img src="{{ asset('template1/img/icon/heart.png') }}" alt="Wishlist"></a></li>
                                                <li><a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}"><img src="{{ asset('template1/img/icon/search.png') }}" alt="Details"></a></li>
                                            </ul>
                                        </div>
                                        <div class="product__item__text">
                                            <h6><a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}">{{ $product->name }}</a></h6>
                                            
                                            {{-- PERBAIKAN: Menggunakan tag <a> dengan kelas yang benar untuk gaya & event listener --}}
                                            <a href="#" class="add-cart add-cart-button" 
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->name }}"
                                                    data-product-price="{{ $product->price }}"
                                                    data-product-image="{{ asset('storage/' . $product->main_image) }}"
                                                    data-product-variants="{{ json_encode($product->variants) }}">
                                                + Add To Cart
                                            </a>

                                            <div class="rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa {{ ($product->rating ?? 0) >= $i ? 'fa-star' : (($product->rating ?? 0) > $i - 1 ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                                @endfor
                                            </div>
                                            <h5>Rp {{ number_format($product->price, 0, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-lg-12 text-center py-5">
                                    <h4>Tidak ada produk yang cocok.</h4>
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
            </form>
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Toast Notification -->
    <div id="toast-notification" class="toast-notification"></div>

    <!-- Variant Selection Modal -->
    <div id="variant-modal" class="variant-modal">
        <div class="variant-modal-content">
            <span class="variant-modal-close">&times;</span>
            <div id="modal-product-info" class="variant-product-info">
                {{-- Info produk akan diisi oleh JavaScript --}}
            </div>
            <form id="variant-form">
                <input type="hidden" id="modal-product-id" name="product_id">
                <div id="modal-variant-selection" class="variant-selection">
                    {{-- Pilihan varian akan diisi oleh JavaScript --}}
                </div>
                <div class="form-group">
                    <label for="modal-quantity">Quantity</label>
                    <input type="number" id="modal-quantity" name="quantity" value="1" min="1" class="form-control">
                </div>
                <button type="submit" id="confirm-add-to-cart" class="site-btn" style="width: 100%; border: none;">Add to Cart</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const isPreview = {{ $isPreview ? 'true' : 'false' }}; // Ambil status pratinjau

            if (!csrfToken && !isPreview) {
                console.error('CSRF token not found!');
            }
            
            // ... (Kode inisialisasi Nice Select & Price Slider tidak berubah) ...

            // --- Logika Modal Varian ---
            const modal = document.getElementById('variant-modal');
            const closeModalBtn = document.querySelector('.variant-modal-close');
            
            document.querySelectorAll('.add-cart-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah navigasi dari tag <a>
                    
                    // Jangan buka modal jika dalam mode pratinjau
                    if (isPreview) {
                        showToast('Fitur ini tidak tersedia dalam mode pratinjau.', 'error');
                        return;
                    }

                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;
                    const productPrice = parseFloat(this.dataset.productPrice);
                    const productImage = this.dataset.productImage;
                    const variants = JSON.parse(this.dataset.productVariants);

                    document.getElementById('modal-product-id').value = productId;
                    const productInfoContainer = document.getElementById('modal-product-info');
                    productInfoContainer.innerHTML = `
                        <img src="${productImage}" onerror="this.onerror=null;this.src='https://placehold.co/80x80/f1f5f9/cbd5e1?text=No+Image';" alt="${productName}">
                        <div class="variant-product-details">
                            <h6>${productName}</h6>
                            <p>Rp ${productPrice.toLocaleString('id-ID')}</p>
                        </div>
                    `;

                    const variantSelectionContainer = document.getElementById('modal-variant-selection');
                    variantSelectionContainer.innerHTML = '';

                    if (variants && variants.length > 0) {
                        const sizes = [...new Set(variants.map(v => v.size).filter(v => v))];
                        const colors = [...new Set(variants.map(v => v.color).filter(v => v))];

                        if (sizes.length > 0) {
                            let sizeOptions = sizes.map(s => `<option value="${s}">${s}</option>`).join('');
                            variantSelectionContainer.innerHTML += `
                                <div class="form-group">
                                    <label for="modal-size">Size</label>
                                    <select id="modal-size" name="size" required>${sizeOptions}</select>
                                </div>
                            `;
                        }
                        if (colors.length > 0) {
                            let colorOptions = colors.map(c => `<option value="${c}">${c}</option>`).join('');
                            variantSelectionContainer.innerHTML += `
                                <div class="form-group">
                                    <label for="modal-color">Color</label>
                                    <select id="modal-color" name="color" required>${colorOptions}</select>
                                </div>
                            `;
                        }
                    }
                    
                    modal.style.display = 'block';
                });
            });

            // Tutup Modal
            if(modal) {
                closeModalBtn.onclick = () => modal.style.display = "none";
                window.onclick = (event) => {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                };
            }

            // Submit form dari dalam modal
            const variantForm = document.getElementById('variant-form');
            if(variantForm) {
                variantForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!csrfToken) {
                        showToast('Terjadi kesalahan. Coba refresh halaman.', 'error');
                        return;
                    }

                    const confirmBtn = document.getElementById('confirm-add-to-cart');
                    const originalText = confirmBtn.innerHTML;
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = 'Adding...';

                    const formData = new FormData(this);
                    const data = {
                        product_id: formData.get('product_id'),
                        quantity: formData.get('quantity'),
                        size: formData.get('size'),
                        color: formData.get('color')
                    };

                    fetch("{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Produk berhasil ditambahkan!', 'success');
                            const cartCountElement = document.getElementById('cart-count');
                            if (cartCountElement && data.cart_count !== undefined) {
                                cartCountElement.textContent = data.cart_count;
                            }
                            modal.style.display = "none";
                        } else {
                            showToast(data.message || 'Gagal menambahkan produk.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Add to Cart Error:', error);
                        showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                    })
                    .finally(() => {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = originalText;
                    });
                });
            }

            // ... (Sisa kode Anda seperti Toast, Wishlist, Facebook Pixel, dll.) ...
            function showToast(message, type = 'success') {
                const toastElement = document.getElementById('toast-notification');
                if (!toastElement) return;
                clearTimeout(window.toastTimeout);
                toastElement.textContent = message;
                toastElement.className = 'toast-notification';
                toastElement.classList.add(type, 'show');
                window.toastTimeout = setTimeout(() => toastElement.classList.remove('show'), 3000);
            }
        });
    </script>
@endpush
