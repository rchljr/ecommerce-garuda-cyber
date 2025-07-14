@extends('template2.layouts.template2')

@section('title', 'Keranjang Belanja')

@section('content')
@php
    // Persiapan variabel dasar
    $isPreview = $isPreview ?? false;
    $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;

    // Hitung subtotal di sini karena controller tidak mengirimkannya
    $subtotal = 0;
    if(isset($cartItems)) {
        foreach($cartItems as $item) {
            $price = optional($item->variant)->price ?? $item->product->price;
            $subtotal += $price * $item->quantity;
        }
    }
@endphp

<div class="hero-wrap hero-bread" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
            <div class="col-md-9 ftco-animate text-center">
                <p class="breadcrumbs">
                    <span class="mr-2"><a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a></span>
                    <span>Keranjang Belanja</span>
                </p>
                <h1 class="mb-0 bread">Keranjang Saya</h1>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section ftco-cart">
    <div class="container">
        @if(isset($cartItems) && $cartItems->count() > 0)
            <div class="row">
                <div class="col-md-12 ftco-animate">
                    <div class="cart-list">
                        <table class="table">
                            <thead class="thead-primary">
                                <tr class="text-center">
                                    <th><input type="checkbox" id="select-all-items"></th>
                                    <th>Gambar</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    @php
                                        // Ambil harga dari varian jika ada, jika tidak, dari produk utama
                                        $price = optional($item->variant)->price ?? $item->product->price;
                                    @endphp
                                    <tr class="text-center cart-item" data-id="{{ $item->id }}">
                                        <td class="product-remove">
                                            <input type="checkbox" class="cart-item-checkbox" value="{{ $item->id }}">
                                        </td>
                                        
                                        <td class="image-prod"><div class="img" style="background-image:url({{ asset('storage/' . $item->product->main_image) }});"></div></td>
                                        
                                        <td class="product-name">
                                            <h3>{{ $item->product->name }}</h3>
                                            @if($item->variant)
                                                <p>Varian: {{ $item->variant->size ?? '' }} {{ $item->variant->color ?? '' }}</p>
                                            @endif
                                        </td>
                                        
                                        <td class="price" data-price="{{ $price }}">Rp{{ number_format($price, 0, ',', '.') }}</td>
                                        
                                        <td class="quantity">
                                            <div class="input-group mb-3">
                                                <input type="number" name="quantity" class="quantity form-control input-number update-quantity" value="{{ $item->quantity }}" min="1" max="100">
                                            </div>
                                        </td>
                                        
                                        <td class="total">Rp{{ number_format($price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr><!-- END TR-->
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row justify-content-between">
                <div class="col-lg-6 mt-3">
                    <button id="remove-selected-btn" class="btn btn-danger py-3 px-4">Hapus Item Terpilih</button>
                </div>
                <div class="col-lg-4 mt-5 cart-wrap ftco-animate">
                    <div class="cart-total mb-3">
                        <h3>Total Keranjang</h3>
                        <p class="d-flex">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </p>
                        <p class="d-flex">
                            <span>Pengiriman</span>
                            <span>Rp0</span>
                        </p>
                        <hr>
                        <p class="d-flex total-price">
                            <span>Total</span>
                            <span id="cart-total">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </p>
                    </div>
                    <p><a href="#" class="btn btn-primary py-3 px-4">Lanjut ke Checkout</a></p>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12 text-center ftco-animate">
                    <h3>Keranjang belanja Anda kosong.</h3>
                    <p>Silakan tambahkan produk ke keranjang terlebih dahulu.</p>
                    <p><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}" class="btn btn-primary">Mulai Belanja</a></p>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const isPreview = {{ $isPreview ? 'true' : 'false' }};

    function showLoading(element, show = true) {
        // Fungsi untuk menampilkan/menyembunyikan overlay loading jika perlu
    }

    function recalculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const price = parseFloat(item.querySelector('.price').dataset.price);
            const quantity = parseInt(item.querySelector('.update-quantity').value);
            const itemTotal = price * quantity;
            item.querySelector('.total').textContent = 'Rp' + itemTotal.toLocaleString('id-ID');
            subtotal += itemTotal;
        });
        document.getElementById('cart-subtotal').textContent = 'Rp' + subtotal.toLocaleString('id-ID');
        document.getElementById('cart-total').textContent = 'Rp' + subtotal.toLocaleString('id-ID');
    }

    // --- Event Listeners ---

    // Update kuantitas
    document.querySelectorAll('.update-quantity').forEach(input => {
        input.addEventListener('change', function() {
            if (isPreview) return;
            const cartItemId = this.closest('.cart-item').dataset.id;
            const quantity = this.value;

            fetch(`{{ !$isPreview ? route('tenant.cart.update', ['subdomain' => $currentSubdomain, 'productCartId' => 'REPLACE_ID']) : '#' }}`.replace('REPLACE_ID', cartItemId), {
                method: 'PATCH',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    recalculateTotals(); // Hitung ulang total di frontend
                    document.getElementById('cart-count').textContent = data.cart_count;
                } else {
                    alert(data.message || 'Gagal memperbarui kuantitas.');
                }
            }).catch(err => console.error(err));
        });
    });

    // Hapus item yang dipilih
    document.getElementById('remove-selected-btn')?.addEventListener('click', function() {
        if (isPreview) return;
        const selectedIds = Array.from(document.querySelectorAll('.cart-item-checkbox:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Pilih item yang ingin dihapus.');
            return;
        }

        if (!confirm(`Anda yakin ingin menghapus ${selectedIds.length} item yang dipilih?`)) return;

        fetch("{{ !$isPreview ? route('tenant.cart.remove', ['subdomain' => $currentSubdomain]) : '#' }}", {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                selectedIds.forEach(id => {
                    document.querySelector(`.cart-item[data-id="${id}"]`)?.remove();
                });
                recalculateTotals();
                document.getElementById('cart-count').textContent = data.cart_count;
                if (document.querySelectorAll('.cart-item').length === 0) {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Gagal menghapus item.');
            }
        }).catch(err => console.error(err));
    });

    // Pilih semua item
    document.getElementById('select-all-items')?.addEventListener('change', function() {
        document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});
</script>
@endpush
