@extends('layouts.customer')
@section('title', 'Keranjang Belanja')

@push('styles')
    {{-- Font Awesome untuk ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Style untuk tombol kuantitas yang lebih baik */
        input[type='number']::-webkit-inner-spin-button,
        input[type='number']::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type='number'] {
            -moz-appearance: textfield;
        }

        .disabled-item {
            opacity: 0.6;
            background-color: #f9fafb;
            /* gray-50 */
        }

        .stock-out-badge {
            background-color: #fee2e2;
            /* red-100 */
            color: #b91c1c;
            /* red-700 */
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 9999px;
            font-weight: 600;
            border: 1px solid #fca5a5;
            /* red-300 */
        }
    </style>
@endpush

@section('content')
    @php
        // 1. Kelompokkan item berdasarkan ID toko
        $groupedItems = $cartItems->groupBy(function ($item) {
            return optional($item->product->shop)->id ?? 'unknown_shop';
        });

        // 2. [MODIFIKASI] Urutkan grup berdasarkan item yang paling baru ditambahkan di setiap grup
        $sortedGroupedItems = $groupedItems->sortByDesc(function ($itemsInGroup) {
            // ->max('created_at') akan mencari nilai 'created_at' paling baru di dalam koleksi item grup tersebut
            return $itemsInGroup->max('created_at');
        });
    @endphp

    <div class="bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Keranjang Belanja Anda</h1>
            <p class="text-gray-600 mb-8">Periksa kembali pesanan Anda sebelum melanjutkan ke checkout.</p>

            @if ($cartItems->isEmpty())
                {{-- Tampilan Keranjang Kosong --}}
                <div class="text-center bg-white p-16 rounded-lg shadow-md">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Keranjang Anda masih kosong</h3>
                    <p class="mt-1 text-sm text-gray-500">Ayo temukan produk favorit Anda!</p>
                    <div class="mt-6">
                        <a href="{{ route('tenant.shop', ['subdomain' => $currentSubdomain]) }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Mulai Belanja
                        </a>
                    </div>
                </div>
            @else
                <form id="cart-form" action="{{ route('tenant.checkout.index', ['subdomain' => $currentSubdomain]) }}"
                    method="GET">
                    <div class="flex flex-col lg:flex-row gap-8 items-start">

                        <!-- Kolom Kiri: Daftar Item Keranjang -->
                        <div class="w-full lg:w-2/3 space-y-4">
                            {{-- Kontrol Pilih Semua & Hapus --}}
                            <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" id="select-all-items"
                                        class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-700">Pilih Semua</span>
                                </label>
                                <button type="button" id="remove-selected-btn"
                                    class="text-sm text-red-600 hover:text-red-800 disabled:text-gray-400 disabled:cursor-not-allowed font-medium"
                                    disabled>
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </button>
                            </div>

                            {{-- [MODIFIKASI] Loop untuk setiap toko menggunakan data yang sudah diurutkan --}}
                            @foreach ($sortedGroupedItems as $shopId => $items)
                                @php
                                    $firstItem = $items->first();
                                    $shop = optional($firstItem->product)->shop;
                                @endphp

                                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                                    {{-- Header Toko --}}
                                    <div class="bg-gray-50 p-4 flex justify-between items-center border-b border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-store text-gray-500"></i>
                                            <span
                                                class="font-bold text-gray-800">{{ optional($shop)->shop_name ?? 'Toko Tidak Dikenal' }}</span>
                                        </div>
                                        @if($shop && $shop->subdomain)
                                            <a href="{{ route('tenant.home', ['subdomain' => $shop->subdomain->subdomain_name]) }}"
                                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5 transition-colors">
                                                Lihat Toko <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        @endif
                                    </div>

                                    {{-- Daftar Item per Toko --}}
                                    <div class="divide-y divide-gray-200">
                                        @foreach ($items as $item)
                                            @php
                                                $product = $item->product;
                                                $variant = $item->variant;
                                                $itemId = $item->id_for_cart;
                                                $isOutOfStock = !$variant || $variant->stock <= 0;
                                            @endphp

                                            <!-- [FIX] Mengubah tata letak item agar responsif -->
                                            <div class="cart-item p-4 {{ $isOutOfStock ? 'disabled-item' : '' }}"
                                                data-id="{{ $itemId }}">
                                                <div class="flex gap-4 items-start">
                                                    <input type="checkbox" name="items[]" value="{{ $itemId }}"
                                                        class="item-checkbox h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mt-1 flex-shrink-0"
                                                        data-price="{{ $variant->price ?? 0 }}" {{ $isOutOfStock ? 'disabled' : '' }}>

                                                    <img src="{{ $item->image ?? 'https://placehold.co/200x200/f1f5f9/cbd5e1?text=Produk' }}"
                                                        alt="{{ $product->name ?? 'Produk' }}"
                                                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-md object-cover flex-shrink-0">

                                                    <div class="flex-grow">
                                                        <a href="{{ route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) }}"
                                                            class="font-semibold text-gray-800 hover:text-indigo-600 leading-tight">{{ $product->name ?? 'Nama Produk' }}</a>
                                                        <p class="text-sm text-gray-500 mt-1">Varian: {{ $variant->name ?? 'N/A' }}</p>
                                                        <p class="text-lg font-bold text-gray-900 mt-2 sm:hidden">
                                                            {{ format_rupiah($variant->price ?? 0) }}</p>
                                                        @if($isOutOfStock)
                                                            <span class="stock-out-badge mt-1 inline-block">Stok Habis</span>
                                                        @endif
                                                    </div>

                                                    <div class="hidden sm:block flex-shrink-0 text-right">
                                                        <p class="text-lg font-bold text-gray-900">
                                                            {{ format_rupiah($variant->price ?? 0) }}</p>
                                                    </div>
                                                </div>

                                                <!-- [FIX] Kontrol kuantitas dan hapus dibuat menjadi baris terpisah di mobile -->
                                                <div class="flex justify-end items-center mt-4">
                                                    <div class="flex items-center border border-gray-300 rounded-md">
                                                        <button type="button"
                                                            class="quantity-btn px-3 py-1 text-lg font-medium text-gray-600 hover:bg-gray-100"
                                                            data-action="decrease" {{ $isOutOfStock ? 'disabled' : '' }}>-</button>
                                                        <input type="number"
                                                            class="w-12 text-center border-l border-r border-gray-300 p-1 quantity-input"
                                                            value="{{ $item->quantity }}" min="1" max="{{ $variant->stock ?? 1 }}" {{ $isOutOfStock ? 'disabled' : '' }}>
                                                        <button type="button"
                                                            class="quantity-btn px-3 py-1 text-lg font-medium text-gray-600 hover:bg-gray-100"
                                                            data-action="increase" {{ $isOutOfStock ? 'disabled' : '' }}>+</button>
                                                    </div>
                                                    <button type="button" class="remove-item-btn text-gray-400 hover:text-red-600 ml-4"
                                                        title="Hapus item">
                                                        <i class="fas fa-trash-alt fa-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Kolom Kanan: Ringkasan & Pembayaran -->
                        <div class="w-full lg:w-1/3">
                            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24 border border-gray-200">
                                <h2 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-4">Ringkasan Pesanan</h2>
                                <div class="space-y-3 mt-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal (<span id="selected-items-count">0</span>
                                            item)</span>
                                        <span id="subtotal-price"
                                            class="font-semibold text-gray-800">{{ format_rupiah(0) }}</span>
                                    </div>
                                </div>

                                @guest('customers')
                                    <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain, 'redirect' => route('tenant.cart.index', ['subdomain' => $currentSubdomain])]) }}"
                                        class="block text-center w-full mt-6 bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-all duration-300">
                                        Login untuk Checkout
                                    </a>
                                @else
                                    <button type="submit" id="checkout-btn"
                                        class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-black disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-300"
                                        disabled>
                                        Lanjut ke Checkout
                                    </button>
                                @endguest
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Logika JavaScript Anda (tanpa perubahan, sudah baik)
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const checkoutBtn = document.getElementById('checkout-btn');
            const removeSelectedBtn = document.getElementById('remove-selected-btn');
            const selectAllCheckbox = document.getElementById('select-all-items');

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function updateSummary() {
                let subtotal = 0;
                let selectedCount = 0;
                let allCheckableItems = 0;
                let allCheckedItems = 0;

                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    if (!checkbox.disabled) {
                        allCheckableItems++;
                        if (checkbox.checked) {
                            allCheckedItems++;
                            const cartItem = checkbox.closest('.cart-item');
                            const price = parseFloat(checkbox.dataset.price);
                            const quantity = parseInt(cartItem.querySelector('.quantity-input').value);
                            subtotal += price * quantity;
                            selectedCount++;
                        }
                    }
                });

                document.getElementById('subtotal-price').textContent = formatRupiah(subtotal);
                document.getElementById('selected-items-count').textContent = selectedCount;

                if (checkoutBtn) checkoutBtn.disabled = (selectedCount === 0);
                if (removeSelectedBtn) removeSelectedBtn.disabled = (selectedCount === 0);
                if (selectAllCheckbox) selectAllCheckbox.checked = (allCheckableItems > 0 && allCheckableItems === allCheckedItems);
            }

            let updateTimeout;
            function updateCartItem(itemId, quantity, inputElement) {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    if (!csrfToken) return;

                    const updateUrl = `{{ route('tenant.cart.update', ['subdomain' => $currentSubdomain, 'productCartId' => '__ID__']) }}`.replace('__ID__', itemId);

                    fetch(updateUrl, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ quantity: quantity })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const cartCountEl = document.getElementById('cart-count');
                                if (cartCountEl) cartCountEl.textContent = data.cart_count;
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                                const maxStock = inputElement.getAttribute('max');
                                if (maxStock) inputElement.value = maxStock;
                            }
                            updateSummary();
                        })
                        .catch(error => console.error('Error updating cart:', error));
                }, 500);
            }

            function handleRemove(itemIds) {
                if (!csrfToken || itemIds.length === 0) return;

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Anda akan menghapus ${itemIds.length} item dari keranjang.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6e7881',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ route('tenant.cart.remove', ['subdomain' => $currentSubdomain]) }}`, {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ ids: itemIds })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    itemIds.forEach(id => {
                                        document.querySelector(`.cart-item[data-id="${id}"]`)?.remove();
                                    });

                                    document.querySelectorAll('.shop-container').forEach(container => {
                                        if (container.querySelectorAll('.cart-item').length === 0) {
                                            container.remove();
                                        }
                                    });

                                    const cartCountEl = document.getElementById('cart-count');
                                    if (cartCountEl) {
                                        cartCountEl.textContent = data.cart_count;
                                    }

                                    updateSummary();
                                    Swal.fire('Dihapus!', data.message, 'success');

                                    if (document.querySelectorAll('.cart-item').length === 0) {
                                        window.location.reload();
                                    }
                                } else {
                                    Swal.fire('Gagal', data.message || 'Gagal menghapus item.', 'error');
                                }
                            }).catch(err => {
                                console.error('Fetch Error:', err);
                                Swal.fire('Error', 'Terjadi kesalahan.', 'error');
                            });
                    }
                });
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', (e) => {
                    document.querySelectorAll('.item-checkbox:not(:disabled)').forEach(checkbox => {
                        checkbox.checked = e.target.checked;
                    });
                    updateSummary();
                });
            }

            document.querySelectorAll('.cart-item').forEach(cartItem => {
                const quantityInput = cartItem.querySelector('.quantity-input');
                const itemId = cartItem.dataset.id;

                cartItem.querySelector('.quantity-btn[data-action="increase"]')?.addEventListener('click', () => {
                    quantityInput.stepUp();
                    updateSummary();
                    updateCartItem(itemId, quantityInput.value, quantityInput);
                });

                cartItem.querySelector('.quantity-btn[data-action="decrease"]')?.addEventListener('click', () => {
                    quantityInput.stepDown();
                    updateSummary();
                    updateCartItem(itemId, quantityInput.value, quantityInput);
                });

                cartItem.querySelector('.remove-item-btn')?.addEventListener('click', () => handleRemove([itemId]));
                cartItem.querySelector('.item-checkbox')?.addEventListener('change', updateSummary);
            });

            if (removeSelectedBtn) {
                removeSelectedBtn.addEventListener('click', () => {
                    const idsToRemove = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
                    handleRemove(idsToRemove);
                });
            }

            updateSummary();
        });
    </script>
@endpush