<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - {{ $tenant->subdomain->subdomain_name ?? 'Toko Anda' }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .cart-item-card {
            transition: box-shadow .3s;
        }
        .cart-item-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        .summary-card {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>

    {{-- Asumsi Anda memiliki file layout/navbar, jika tidak, Anda bisa menambahkannya di sini --}}
    {{-- @include('template1.partials.navbar') --}}

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4 fw-bold">Keranjang Belanja</h1>
            </div>
        </div>

        @if($cartItems && count($cartItems) > 0)
            <div class="row">
                {{-- Daftar Item Keranjang --}}
                <div class="col-lg-8">
                    @foreach($cartItems as $item)
                        <div class="card mb-3 cart-item-card" id="cart-item-{{ $item->id }}">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-md-row align-items-center">
                                    <!-- Gambar Produk -->
                                    <img src="{{ $item->product->image_url ?? 'https://placehold.co/100x100/EFEFEF/AAAAAA?text=Produk' }}" 
                                         class="img-fluid rounded me-md-4 mb-3 mb-md-0" 
                                         alt="{{ $item->product->name }}" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                    
                                    <!-- Detail Produk -->
                                    <div class="flex-grow-1">
                                        <h5 class="card-title fw-bold">{{ $item->product->name }}</h5>
                                        <p class="card-text text-muted small">
                                            Varian: {{ $item->size }} / {{ $item->color }}
                                        </p>
                                        <p class="card-text fw-semibold">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                    </div>

                                    <!-- Kuantitas -->
                                    <div class="d-flex align-items-center my-3 my-md-0 mx-md-4">
                                        <input type="number" 
                                               class="form-control quantity-input" 
                                               value="{{ $item->quantity }}" 
                                               min="1"
                                               data-id="{{ $item->id }}"
                                               onchange="updateQuantity('{{ $item->id }}', this.value)">
                                    </div>

                                    <!-- Subtotal & Hapus -->
                                    <div class="text-end" style="min-width: 120px;">
                                        <p class="fw-bold fs-5 mb-2" id="subtotal-{{ $item->id }}">
                                            Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                        </p>
                                        <button class="btn btn-sm btn-outline-danger" onclick="removeItem('{{ $item->id }}')">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Ringkasan Belanja --}}
                <div class="col-lg-4">
                    <div class="card summary-card">
                        <div class="card-body">
                            <h4 class="card-title fw-bold mb-4">Ringkasan Belanja</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-semibold" id="summary-subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Ongkos Kirim</span>
                                <span class="fw-semibold">Rp 0</span> {{-- Ganti dengan logika ongkir jika ada --}}
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                                <span>Total</span>
                                <span id="summary-total">Rp 0</span>
                            </div>
                            <div class="d-grid">
                                <a href="#" class="btn btn-primary btn-lg">
                                    Lanjutkan ke Checkout <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Keranjang Kosong --}}
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-cart-x" style="font-size: 5rem; color: #6c757d;"></i>
                <h2 class="mt-4">Keranjang Anda Kosong</h2>
                <p class="text-muted">Sepertinya Anda belum menambahkan produk apapun.</p>
                <a href="/" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Kembali Belanja
                </a>
            </div>
        @endif
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Data keranjang dari PHP ke JavaScript
        let cartItems = {!! json_encode($cartItems->keyBy('id')) !!};

        function calculateTotals() {
            let subtotal = 0;
            for (const id in cartItems) {
                const item = cartItems[id];
                subtotal += item.product.price * item.quantity;
            }

            document.getElementById('summary-subtotal').innerText = `Rp ${subtotal.toLocaleString('id-ID')}`;
            document.getElementById('summary-total').innerText = `Rp ${subtotal.toLocaleString('id-ID')}`; // Ganti dengan logika total + ongkir
        }

        function updateQuantity(itemId, newQuantity) {
            // Update kuantitas di objek JS
            cartItems[itemId].quantity = parseInt(newQuantity);
            
            // Update tampilan subtotal item
            const itemSubtotal = cartItems[itemId].product.price * cartItems[itemId].quantity;
            document.getElementById(`subtotal-${itemId}`).innerText = `Rp ${itemSubtotal.toLocaleString('id-ID')}`;
            
            // Hitung ulang total ringkasan
            calculateTotals();

            // Kirim request ke server (opsional, jika ingin menyimpan perubahan secara real-time)
            // fetch(`/cart/update/${itemId}`, { ... });
        }

        function removeItem(itemId) {
            if (!confirm('Anda yakin ingin menghapus item ini?')) {
                return;
            }

            // Hapus dari objek JS
            delete cartItems[itemId];

            // Hapus elemen dari DOM
            document.getElementById(`cart-item-${itemId}`).remove();

            // Hitung ulang total
            calculateTotals();
            
            // Cek jika keranjang jadi kosong
            if (Object.keys(cartItems).length === 0) {
                location.reload(); // Refresh halaman untuk menampilkan pesan keranjang kosong
            }

            // Kirim request ke server untuk menghapus
            // fetch(`/cart/remove`, { method: 'POST', body: JSON.stringify({ ids: [itemId] }), ... });
        }

        // Hitung total saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', function() {
            if (Object.keys(cartItems).length > 0) {
                calculateTotals();
            }
        });
    </script>

</body>
</html>
