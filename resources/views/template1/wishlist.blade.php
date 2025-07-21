@extends('template1.layouts.template')

@section('title', 'Wishlist Saya')

@push('styles')
    {{-- Style yang konsisten untuk Notifikasi dan Modal Varian --}}
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
        .primary-btn:disabled { background-color: #b0b0b0 !important; cursor: not-allowed; }
        
        /* Gaya untuk tombol aksi di tabel */
        .wishlist-actions .primary-btn, .wishlist-actions .outline-btn {
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
                                            <tr data-wishlist-row="{{ $item->product->id }}">
                                                <td class="product__cart__item">
                                                    <div class="product__cart__item__pic">
                                                        <img src="{{ $item->product->image_url ?? '' }}" alt="{{ $item->product->name }}" style="width: 90px;">
                                                    </div>
                                                    <div class="product__cart__item__text">
                                                        <h6>{{ $item->product->name }}</h6>
                                                    </div>
                                                </td>
                                                <td class="cart__price">
                                                    {{ format_rupiah($item->product->price) }}
                                                </td>
                                                <td class="cart__close text-center wishlist-actions">
                                                    {{-- PERBAIKAN: Tombol diubah untuk menggunakan AJAX --}}
                                                    <button class="primary-btn open-variant-modal" data-product-id="{{ $item->product->id }}">+ Keranjang</button>
                                                    <button class="outline-btn remove-wishlist-item" data-product-id="{{ $item->product->id }}">Hapus</button>
                                                </td>
                                            </tr>
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
                            <a href="{{ route('tenant.shop', ['subdomain' => $currentSubdomain]) }}" class="primary-btn mt-4">Mulai Belanja</a>
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
            return [$item->product->id => $item->product->load('variants')];
        }));

        document.addEventListener('DOMContentLoaded', function() {
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
                            
                            // Update header count
                            const wishlistCountEl = document.getElementById('wishlist-count');
                            if(wishlistCountEl) wishlistCountEl.textContent = res.data.wishlist_count;
                            
                            // Hapus baris dari tabel dengan animasi
                            const row = document.querySelector(`tr[data-wishlist-row="${productId}"]`);
                            if (row) {
                                row.style.transition = 'opacity 0.5s ease';
                                row.style.opacity = '0';
                                setTimeout(() => {
                                    row.remove();
                                    // Cek jika tabel menjadi kosong
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

            // --- Logika Modal (Sama seperti halaman Toko) ---
            function initializeModal() {
                const modal = document.getElementById('variant-modal');
                const modalBody = document.getElementById('modal-body');
                
                document.querySelectorAll('.open-variant-modal').forEach(button => {
                    button.addEventListener('click', function(e) {
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
                const uniqueSizes = [...new Set(product.variants.map(v => v.size).filter(Boolean))];
                let sizesHTML = uniqueSizes.map(size => `<option value="${size}">${size}</option>`).join('');

                modalBody.innerHTML = `
                    <div>
                        <h4 class="font-bold text-lg">${product.name}</h4>
                        <p class="text-red-600 font-bold text-lg">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(product.price)}</p>
                    </div>
                    <form id="modal-cart-form" class="space-y-4 mt-4" novalidate>
                        <input type="hidden" name="product_id" value="${product.id}">
                        ${sizesHTML ? `<div>
                            <label for="size-select" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                            <select id="size-select" name="size" class="modal-variant-select" required>
                                <option value="">Pilih Ukuran</option>
                                ${sizesHTML}
                            </select>
                        </div>` : ''}
                        <div>
                            <label for="color-select" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                            <select id="color-select" name="color" class="modal-variant-select" ${!sizesHTML ? '' : 'disabled'} required>
                                <option value="">${sizesHTML ? 'Pilih ukuran dahulu' : 'Pilih Warna'}</option>
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
                if (!sizesHTML) {
                    const colorSelect = document.getElementById('color-select');
                    const availableColors = [...new Set(product.variants.map(v => v.color).filter(Boolean))];
                    availableColors.forEach(color => colorSelect.add(new Option(color, color)));
                    colorSelect.disabled = false;
                }
            }

            function attachModalEventListeners(product) {
                const form = document.getElementById('modal-cart-form');
                const sizeSelect = document.getElementById('size-select');
                const colorSelect = document.getElementById('color-select');
                const modalAddBtn = document.getElementById('modal-add-btn');

                const updateColors = () => {
                    const selectedSize = sizeSelect.value;
                    colorSelect.innerHTML = '<option value="">Pilih Warna</option>';
                    colorSelect.disabled = true;
                    if (selectedSize) {
                        const availableColors = [...new Set(product.variants.filter(v => v.size === selectedSize).map(v => v.color))];
                        if (availableColors.length > 0) {
                            availableColors.forEach(color => {
                                const variant = product.variants.find(v => v.size === selectedSize && v.color === color);
                                const option = new Option(color, color);
                                if (variant.stock <= 0) {
                                    option.disabled = true;
                                    option.textContent += ' (Habis)';
                                }
                                colorSelect.add(option);
                            });
                            colorSelect.disabled = false;
                        } else {
                            colorSelect.innerHTML = '<option value="">Warna tidak ada</option>';
                        }
                    } else {
                        colorSelect.innerHTML = '<option value="">Pilih ukuran dahulu</option>';
                    }
                    updateButtonState();
                };

                const updateButtonState = () => {
                    const hasSize = !!sizeSelect;
                    const sizeValue = hasSize ? sizeSelect.value : true;
                    const colorValue = colorSelect.value;
                    modalAddBtn.disabled = !(sizeValue && colorValue);
                };

                if (sizeSelect) sizeSelect.addEventListener('change', updateColors);
                colorSelect.addEventListener('change', updateButtonState);
                form.addEventListener('submit', handleFormSubmit);
                updateButtonState();
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = 'Memproses...';

                const data = Object.fromEntries(new FormData(form).entries());

                axios.post(`/tenant/${subdomain}/cart/add`, data)
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
                    .catch(err => showToast(err.response?.data?.message || 'Terjadi kesalahan.', 'error'))
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
