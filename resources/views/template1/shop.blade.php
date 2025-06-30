@extends('template1.layouts.template1')

@section('title', 'Toko - Male-Fashion')

@push('styles')
{{-- CSS Khusus untuk Notifikasi Toast dan Wishlist Aktif --}}
<style>
    .toast-notification {
        position: fixed; bottom: 20px; right: 20px; background-color: #333;
        color: white; padding: 15px 25px; border-radius: 8px; z-index: 10000;
        opacity: 0; visibility: hidden; transform: translateY(20px);
        transition: all 0.3s ease-in-out; font-family: 'Nunito Sans', sans-serif;
    }
    .toast-notification.show { opacity: 1; visibility: visible; transform: translateY(0); }
    .toast-notification.success { background-color: #28a745; }
    .toast-notification.error { background-color: #dc3545; }
    .product__hover .toggle-wishlist.active img {
        filter: invert(25%) sepia(100%) saturate(5000%) hue-rotate(330deg);
    }
    /* Style baru untuk product item */
    .product__item {
        transition: all 0.3s ease;
        border: 1px solid #f2f2f2;
        border-radius: 5px;
        overflow: hidden;
    }
    .product__item:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-5px);
    }
    .shop__sidebar {
        background-color: #f9f9f9;
        padding: 30px;
        border-radius: 5px;
    }
    .shop__product__option {
        padding: 15px 20px;
        border: 1px solid #f2f2f2;
        border-radius: 5px;
        background-color: #ffffff;
        margin-bottom: 30px;
    }
    /* Style untuk kategori aktif */
    .sidebar__categories ul li a.active {
        color: #111111;
        font-weight: 700;
    }
</style>
@endpush


@section('content')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Shop</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('home') }}">Home</a>
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
            {{-- Form untuk semua filter dan sorting --}}
            <form id="filter-sort-form" method="GET" action="{{ route('shop') }}">
                <div class="row">
                    {{-- Sidebar Filter --}}
                    <div class="col-lg-3 col-md-3">
                        <div class="shop__sidebar">
                            <div class="sidebar__categories mb-4">
                                <div class="section-title">
                                    <h4>Categories</h4>
                                </div>
                                <ul>
                                    {{-- Link kategori menjadi dinamis --}}
                                    <li><a href="{{ route('shop') }}" class="{{ !request('category') ? 'active' : '' }}">All Categories</a></li>
                                    @forelse ($categories as $category)
                                        <li><a href="{{ route('shop', ['category' => $category->slug]) }}" class="{{ request('category') == $category->slug ? 'active' : '' }}">{{ $category->name }}</a></li>
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
                                <button type="submit" class="primary-btn mt-3" style="width: 100%; border: none;">Filter</button>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Produk --}}
                    <div class="col-lg-9 col-md-9">
                        {{-- Kontrol Urutan Produk --}}
                        <div class="shop__product__option">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <p>Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} hasil</p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="select__option">
                                        {{-- Dropdown urutan menjadi dinamis --}}
                                        <select name="sort" id="sort-by" class="nice-select">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
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
                                        <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                            @if ($product->is_new ?? false)
                                                <span class="label new">New</span>
                                            @elseif (($product->discount_percentage ?? 0) > 0)
                                                <span class="label sale">Sale</span>
                                            @endif
                                            <ul class="product__hover">
                                                <li><a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><img src="{{ asset('template1/img/icon/heart.png') }}" alt="Wishlist"></a></li>
                                                <li><a href="{{ route('shop.details', ['product' => $product->slug]) }}"><img src="{{ asset('template1/img/icon/search.png') }}" alt="Details"></a></li>
                                            </ul>
                                        </div>
                                        <div class="product__item__text">
                                            <h6>{{ $product->name }}</h6>
                                            <h6><a href="{{ route('shop.details', ['product' => $product->slug]) }}">{{ $product->name }}</a></h6>
                                            <a href="#" class="add-cart" data-product-id="{{ $product->id }}">+ Add To Cart</a>
                                            <div class="rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa {{ ($product->rating ?? 0) >= $i ? 'fa-star' : (($product->rating ?? 0) > ($i - 1) ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
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
                                {{-- Menambahkan query string agar filter tetap ada saat ganti halaman --}}
                                {{ $products->withQueryString()->links('vendor.pagination.default') }}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- Product Section End -->

    <div id="toast-notification" class="toast-notification"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Inisialisasi Nice Select untuk dropdown sorting ---
        if (typeof jQuery !== 'undefined' && jQuery.fn.niceSelect) {
            $('#sort-by').niceSelect();
        }

        // --- Kirim form secara otomatis saat sorting diubah ---
        document.getElementById('sort-by').addEventListener('change', function() {
            document.getElementById('filter-sort-form').submit();
        });

        // --- Fungsi untuk memformat ulang harga dari slider sebelum submit ---
        document.getElementById('filter-sort-form').addEventListener('submit', function(e) {
            const minAmountInput = document.getElementById('minamount');
            const maxAmountInput = document.getElementById('maxamount');
            
            // Hapus "Rp " dan "." dari nilai harga
            minAmountInput.value = minAmountInput.value.replace(/[^0-9]/g, '');
            maxAmountInput.value = maxAmountInput.value.replace(/[^0-9]/g, '');
        });

        // --- FUNGSI JAVASCRIPT LENGKAP DIMASUKKAN DI SINI ---
        
        // Fungsi untuk Notifikasi Toast
        const toastElement = document.getElementById('toast-notification');
        let toastTimeout;

        function showToast(message, type = 'success') {
            clearTimeout(toastTimeout);
            toastElement.textContent = message;
            toastElement.className = 'toast-notification';
            toastElement.classList.add(type, 'show');
            toastTimeout = setTimeout(() => {
                toastElement.classList.remove('show');
            }, 3000);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found! Pastikan ada <meta name="csrf-token" content="{{ csrf_token() }}"> di layout Anda.');
        }

        // Fungsi untuk AJAX Add to Cart
        document.querySelectorAll('.add-cart').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (!csrfToken) { showToast('Terjadi kesalahan. Coba refresh halaman.', 'error'); return; }

                const productId = this.dataset.productId;
                const originalText = this.textContent;
                
                this.disabled = true;
                this.textContent = 'Adding...';

                fetch("{{ route('cart.add') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Produk berhasil ditambahkan!', 'success');
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement && data.cart_count !== undefined) {
                            cartCountElement.textContent = data.cart_count;
                            cartCountElement.style.transform = 'scale(1.5)';
                            setTimeout(() => { cartCountElement.style.transform = 'scale(1)'; }, 200);
                        }
                    } else {
                        showToast(data.message || 'Gagal menambahkan produk.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Add to Cart Error:', error);
                    showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                })
                .finally(() => {
                    setTimeout(() => { this.disabled = false; this.textContent = originalText; }, 500);
                });
            });
        });

        // Fungsi untuk AJAX Wishlist
        document.querySelectorAll('.toggle-wishlist').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (!csrfToken) { showToast('Terjadi kesalahan. Coba refresh halaman.', 'error'); return; }

                const productId = this.dataset.productId;
                
                fetch("{{ route('wishlist.toggle') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => {
                    if (response.status === 401) {
                        window.location.href = "{{ route('login') }}";
                        throw new Error('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        if(data.action === 'added') {
                            showToast('Produk ditambahkan ke Wishlist!', 'success');
                            this.classList.add('active');
                        } else {
                            showToast('Produk dihapus dari Wishlist.', 'success');
                            this.classList.remove('active');
                        }
                        const wishlistCountElement = document.getElementById('wishlist-count');
                        if(wishlistCountElement) {
                            wishlistCountElement.textContent = data.wishlist_count;
                        }
                    } else {
                        showToast(data.message || 'Operasi wishlist gagal.', 'error');
                    }
                }).catch(error => {
                    if (error.message !== 'Unauthorized') {
                        console.error('Wishlist Error:', error);
                        showToast('Terjadi kesalahan pada wishlist.', 'error');
                    }
                });
            });
        });

        // Inisialisasi Plugin jQuery lain (jika ada)
        if (typeof jQuery !== 'undefined') {
            // Price Range Slider
            if ($.fn.slider) {
                var rangeSlider = $(".price-range"),
                    minamount = $("#minamount"),
                    maxamount = $("#maxamount"),
                    minPrice = rangeSlider.data('min'),
                    maxPrice = rangeSlider.data('max');
                rangeSlider.slider({
                    range: true, min: minPrice, max: maxPrice, values: [minPrice, maxPrice],
                    slide: function (event, ui) {
                        minamount.val('Rp ' + ui.values[0].toLocaleString('id-ID'));
                        maxamount.val('Rp ' + ui.values[1].toLocaleString('id-ID'));
                    }
                });
                minamount.val('Rp ' + rangeSlider.slider("values", 0).toLocaleString('id-ID'));
                maxamount.val('Rp ' + rangeSlider.slider("values", 1).toLocaleString('id-ID'));
            }
        }
    });
</script>
@endpush