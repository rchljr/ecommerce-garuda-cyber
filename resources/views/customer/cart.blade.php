@extends('layouts.customer')
@section('title', 'Keranjang Belanja')

@section('content')
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Keranjang Belanja Anda</h1>
            <form id="cart-form" action="{{ route('checkout.index') }}" method="GET">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Daftar Item Keranjang -->
                    <div class="w-full lg:w-2/3">
                        <div class="bg-white rounded-lg shadow-md p-6 space-y-4">
                            <div class="flex justify-between items-center border-b pb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all-items"
                                        class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-medium">Pilih Semua</span>
                                </label>
                                <button type="button" id="remove-selected-btn"
                                    class="text-sm text-red-600 hover:underline">Hapus yang Dipilih</button>
                            </div>

                            @forelse ($cartItems as $item)
                                <div class="flex items-center border-b py-4 cart-item">
                                    <input type="checkbox" name="items[]" value="{{ $item->id }}"
                                        class="item-checkbox h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        data-price="{{ $item->product->price }}" data-quantity="{{ $item->quantity }}">
                                    <img src="https://placehold.co/80x80" alt="{{ $item->product->name }}"
                                        class="w-20 h-20 rounded-md object-cover mx-4">
                                    <div class="flex-grow">
                                        <p class="font-semibold">{{ $item->product->name }}</p>
                                        <p class="text-sm text-gray-500">Toko:
                                            {{ optional($item->product->shopOwner->shop)->shop_name }}</p>
                                        <p class="text-lg font-bold text-gray-800 mt-1">
                                            {{ format_rupiah($item->product->price) }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" class="quantity-btn" data-action="decrease">-</button>
                                        <input type="number" class="w-12 text-center border-gray-300 rounded-md quantity-input"
                                            value="{{ $item->quantity }}" min="1">
                                        <button type="button" class="quantity-btn" data-action="increase">+</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-10">Keranjang Anda kosong.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Ringkasan Pesanan -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                            <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>
                            <div class="space-y-4 mt-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal (<span id="selected-items-count">0</span>
                                        item)</span>
                                    <span id="subtotal-price" class="font-semibold">{{ format_rupiah(0) }}</span>
                                </div>
                            </div>
                            <button type="submit" id="checkout-btn"
                                class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-black disabled:bg-gray-400"
                                disabled>
                                Checkout
                            </button>
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
            const selectAllCheckbox = document.getElementById('select-all-items');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const checkoutBtn = document.getElementById('checkout-btn');
            const subtotalPriceEl = document.getElementById('subtotal-price');
            const selectedItemsCountEl = document.getElementById('selected-items-count');

            function updateSummary() {
                let subtotal = 0;
                let selectedCount = 0;
                itemCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const price = parseFloat(checkbox.dataset.price);
                        const quantity = parseInt(checkbox.closest('.cart-item').querySelector('.quantity-input').value);
                        subtotal += price * quantity;
                        selectedCount++;
                    }
                });

                subtotalPriceEl.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal);
                selectedItemsCountEl.textContent = selectedCount;
                checkoutBtn.disabled = selectedCount === 0;
            }

            selectAllCheckbox.addEventListener('change', (e) => {
                itemCheckboxes.forEach(checkbox => checkbox.checked = e.target.checked);
                updateSummary();
            });

            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSummary);
            });

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', updateSummary);
            });

            updateSummary(); // Initial calculation
        });
    </script>
@endpush