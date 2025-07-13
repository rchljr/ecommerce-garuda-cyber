@extends('layouts.customer')
@section('title', 'Keranjang Belanja')

@section('content')
    @php
        // Ambil subdomain saat ini untuk digunakan di dalam rute
        $currentSubdomain = request()->route('subdomain');
    @endphp

    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Keranjang Belanja Anda</h1>

            {{-- Form ini akan mengarahkan ke halaman checkout saat disubmit --}}
            <form id="cart-form" action="{{ route('tenant.checkout.index', ['subdomain' => $currentSubdomain]) }}"
                method="GET">
                <div class="flex flex-col lg:flex-row gap-8">

                    <!-- Kolom Kiri: Daftar Item Keranjang -->
                    <div class="w-full lg:w-2/3">
                        <div class="bg-white rounded-lg shadow-md p-6 space-y-4">
                            @if(isset($cartItems) && $cartItems->isNotEmpty())
                                <div class="flex justify-between items-center border-b pb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="select-all-items"
                                            class="h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="ml-3 text-sm font-medium">Pilih Semua</span>
                                    </label>
                                    <button type="button" id="remove-selected-btn"
                                        class="text-sm text-red-600 hover:underline">Hapus yang Dipilih</button>
                                </div>
                            @endif

                            @forelse ($cartItems as $item)
                                @php
                                    // Logika disederhanakan karena data sudah konsisten dari service
                                    $product = $item->product;
                                    $variant = $item->variant; // Ambil objek variant

                                    // Untuk guest, ID item adalah variant_id. Untuk user login, ID item adalah UUID.
                                    $itemId = $item->id;
                                    $quantity = $item->quantity;

                                    // Ambil data varian dari relasi
                                    $color = optional($variant)->color ?? 'N/A';
                                    $size = optional($variant)->size ?? 'N/A';

                                    $slug = optional($product)->slug;
                                @endphp
                                {{-- Gunakan $itemId sebagai data-id dan value checkbox --}}
                                <div class="cart-item flex flex-col sm:flex-row items-start border-b py-4 gap-4" data-id="{{ $itemId }}">
                                    <div class="flex items-start w-full">
                                        <input type="checkbox" name="items[]" value="{{ $itemId }}"
                                            class="item-checkbox h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-500 mt-1"
                                            data-price="{{ $product->price ?? 0 }}">

                                        <img src="{{ asset('storage/' . ($product->main_image ?? 'images/placeholder.png')) }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/80x80/f1f5f9/cbd5e1?text=No+Image';"
                                            alt="{{ $product->name ?? 'Produk tidak ditemukan' }}"
                                            class="w-20 h-20 rounded-md object-cover mx-4">

                                        <div class="flex-grow">
                                            @if($slug)
                                                <a href="{{ route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $slug]) }}"
                                                    class="font-semibold text-gray-800 hover:text-red-600">{{ $product->name ?? 'Nama Produk' }}</a>
                                            @else
                                                <span class="font-semibold text-gray-800">{{ $product->name ?? 'Nama Produk' }}</span>
                                            @endif
                                            <p class="text-sm text-gray-500">
                                                Varian: {{ $color }} / {{ $size }}
                                            </p>
                                            <p class="text-lg font-bold text-gray-800 mt-1 sm:hidden">
                                                {{ format_rupiah($product->price ?? 0) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="w-full sm:w-auto flex justify-between items-center">
                                        <p class="text-lg font-bold text-gray-800 mt-1 hidden sm:block">
                                            {{ format_rupiah($product->price ?? 0) }}
                                        </p>
                                        <div class="flex items-center border border-gray-300 rounded-md ml-auto">
                                            <button type="button" class="quantity-btn px-3 py-1 text-lg" data-action="decrease">-</button>
                                            <input type="number" class="w-12 text-center border-l border-r border-gray-300 quantity-input" value="{{ $quantity }}" min="1">
                                            <button type="button" class="quantity-btn px-3 py-1 text-lg" data-action="increase">+</button>
                                        </div>
                                        <button type="button" class="remove-item-btn text-xs text-gray-500 hover:text-red-600 hover:underline ml-4 sm:hidden">Hapus</button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-16">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" aria-hidden="true">
                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keranjang Anda kosong</h3>
                                    <p class="mt-1 text-sm text-gray-500">Ayo mulai belanja dan temukan produk favorit Anda!</p>
                                    <div class="mt-6">
                                        <a href="{{ route('tenant.shop', ['subdomain' => $currentSubdomain]) }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                            Mulai Belanja
                                        </a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Kolom Kanan: Ringkasan & Pembayaran -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                            <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>
                            <div class="space-y-4 mt-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal (<span id="selected-items-count">0</span>
                                        item)</span>
                                    <span id="subtotal-price"
                                        class="font-semibold text-gray-800">{{ format_rupiah(0) }}</span>
                                </div>
                            </div>

                            @guest('customers')
                                <a href="{{ route('tenant.customer.login.form', ['subdomain' => $currentSubdomain, 'redirect' => route('tenant.cart.index', ['subdomain' => $currentSubdomain])]) }}"
                                    class="block text-center w-full mt-6 bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700">
                                    Login untuk Checkout
                                </a>
                            @else
                                {{-- Tombol Checkout ini adalah tombol submit untuk form di atas --}}
                                <button type="submit" id="checkout-btn"
                                    class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-black disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    disabled>
                                    Checkout
                                </button>
                            @endguest
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ambil elemen dengan aman untuk menghindari error jika tidak ada
            const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenEl ? csrfTokenEl.getAttribute('content') : null;
            const checkoutBtn = document.getElementById('checkout-btn');

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function updateSummary() {
                let subtotal = 0;
                let selectedCount = 0;
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    if (checkbox.checked) {
                        const cartItem = checkbox.closest('.cart-item');
                        const price = parseFloat(checkbox.dataset.price);
                        const quantity = parseInt(cartItem.querySelector('.quantity-input').value);
                        subtotal += price * quantity;
                        selectedCount++;
                    }
                });

                const subtotalPriceEl = document.getElementById('subtotal-price');
                const selectedItemsCountEl = document.getElementById('selected-items-count');

                if (subtotalPriceEl) subtotalPriceEl.textContent = formatRupiah(subtotal);
                if (selectedItemsCountEl) selectedItemsCountEl.textContent = selectedCount;

                if (checkoutBtn) {
                    checkoutBtn.disabled = (selectedCount === 0);
                }
            }

            let updateTimeout;
            function updateCartItem(itemId, quantity) {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    if (!csrfToken) return;

                    // DEBUGGING: Tampilkan ID dan kuantitas yang akan dikirim
                    console.log(`[DEBUG] Mengirim update untuk Item ID: ${itemId}, Kuantitas: ${quantity}`);

                    const updateUrl = `{{ route('tenant.cart.update', ['subdomain' => $currentSubdomain, 'productCartId' => '__ID__']) }}`.replace('__ID__', itemId);

                    fetch(updateUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ quantity: quantity })
                    })
                        .then(response => {
                            if (!response.ok) {
                                console.error(`[DEBUG] Gagal update. Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('[DEBUG] Respons dari server:', data);
                        })
                        .catch(error => {
                            console.error('[DEBUG] Terjadi error saat fetch:', error)
                        });
                }, 500);
            }

            function handleRemove(itemIds) {
                if (!csrfToken) return;
                Swal.fire({
                    title: 'Anda Yakin?', text: `Anda akan menghapus ${itemIds.length} item dari keranjang.`, icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6e7881',
                    confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
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
                                    updateSummary();
                                    const cartCountEl = document.getElementById('cart-count');
                                    if (cartCountEl) cartCountEl.textContent = data.cart_count;
                                    Swal.fire('Dihapus!', data.message, 'success');
                                    if (document.querySelectorAll('.cart-item').length === 0) {
                                        window.location.reload();
                                    }
                                } else {
                                    Swal.fire('Gagal', data.message || 'Gagal menghapus item.', 'error');
                                }
                            }).catch(err => Swal.fire('Error', 'Terjadi kesalahan.', 'error'));
                    }
                });
            }


            // --- Event Listeners ---
            const selectAllCheckbox = document.getElementById('select-all-items');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', (e) => {
                    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                        checkbox.checked = e.target.checked;
                    });
                    updateSummary();
                });
            }

            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSummary);
            });

            document.querySelectorAll('.cart-item').forEach(cartItem => {
                const quantityInput = cartItem.querySelector('.quantity-input');
                // PERBAIKAN KUNCI: Ambil itemId dari elemen cart-item yang benar
                const itemId = cartItem.dataset.id;

                // DEBUGGING: Pastikan itemId yang diambil benar saat halaman dimuat
                console.log(`[DEBUG] Listener terpasang untuk Item ID: ${itemId}`);

                cartItem.querySelector('.quantity-btn[data-action="increase"]')?.addEventListener('click', () => {
                    quantityInput.stepUp();
                    updateSummary();
                    updateCartItem(itemId, quantityInput.value);
                });

                cartItem.querySelector('.quantity-btn[data-action="decrease"]')?.addEventListener('click', () => {
                    if (quantityInput.value > 1) {
                        quantityInput.stepDown();
                        updateSummary();
                        updateCartItem(itemId, quantityInput.value);
                    }
                });

                cartItem.querySelector('.remove-item-btn')?.addEventListener('click', function () {
                    handleRemove([itemId]);
                });
            });

            const removeSelectedBtn = document.getElementById('remove-selected-btn');
            if (removeSelectedBtn) {
                removeSelectedBtn.addEventListener('click', () => {
                    const selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked'));
                    if (selectedItems.length === 0) {
                        Swal.fire('Oops...', 'Silakan pilih item yang ingin dihapus.', 'info');
                        return;
                    }
                    const idsToRemove = selectedItems.map(cb => cb.value);
                    handleRemove(idsToRemove);
                });
            }

            // Panggil sekali saat halaman dimuat untuk memastikan status tombol sudah benar
            updateSummary();
        });
    </script>
@endpush