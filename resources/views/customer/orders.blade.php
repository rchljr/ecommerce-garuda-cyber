@extends('layouts.customer')
@section('title', 'Pesanan Saya')

@push('styles')
<style>
    /* Gaya untuk modal */
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.6);
        animation-name: fadeIn;
        animation-duration: 0.4s;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 8% auto;
        padding: 25px;
        border: 1px solid #888;
        width: 90%;
        max-width: 500px;
        border-radius: 8px;
        animation-name: slideIn;
        animation-duration: 0.4s;
    }
    .modal-close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    /* Gaya untuk rating bintang */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 5px;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        color: #ddd;
        font-size: 2.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #f59e0b; /* amber-500 */
    }
    @keyframes fadeIn { from {opacity: 0} to {opacity: 1} }
    @keyframes slideIn { from {top: -100px; opacity: 0} to {margin-top: 8%} }
</style>
@endpush

@section('content')
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8">

            <!-- Sidebar Kiri -->
            <aside class="w-full md:w-1/4 lg:w-1/5 flex-shrink-0">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    @include('layouts._partials.customer-sidebar')
                </div>
            </aside>

            <!-- Konten Utama Kanan -->
            <main class="w-full md:w-3/4 lg:w-4/5">
                <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold">Pesanan Saya</h1>
                            <p class="text-gray-500 mt-1">Riwayat semua pesanan Anda dari berbagai toko.</p>
                        </div>
                    </div>

                    <!-- Daftar Pesanan -->
                    <div class="space-y-6">
                        @forelse($orders as $order)
                            <div class="border border-gray-200 rounded-lg">
                                {{-- Header Order --}}
                                <div class="bg-gray-50 p-4 flex justify-between items-center rounded-t-lg">
                                    <div>
                                        {{-- : Membuat nama toko dapat diklik --}}
                                        @if($order->subdomain && optional($order->subdomain->user)->shop)
                                            <p class="text-sm text-gray-500">
                                                Toko: 
                                                <a href="/tenant/{{ $order->subdomain->subdomain_name }}" class="font-semibold text-blue-600 hover:underline">
                                                    {{ $order->subdomain->user->shop->shop_name }}
                                                </a>
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-500">Toko: <span class="font-semibold text-gray-800">Toko Dihapus</span></p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">{{ format_tanggal($order->order_date) }}</p>
                                    </div>
                                    <div>
                                        @php
                                            $statusText = '';
                                            $statusClass = '';
                                            if ($order->status == 'completed') {
                                                $statusText = 'Selesai';
                                                $statusClass = 'bg-green-100 text-green-800';
                                            } elseif ($order->status == 'cancelled' || $order->status == 'failed') {
                                                $statusText = 'Dibatalkan';
                                                $statusClass = 'bg-red-100 text-red-800';
                                            } else {
                                                $statusText = 'Diproses';
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                            }
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>
                                {{-- Body Order (Item) --}}
                                <div class="p-4 space-y-4">
                                    @foreach($order->items as $item)
                                        @php
                                            $existingReview = $order->testimonials->firstWhere('product_id', $item->product_id);
                                            // Cek apakah pesanan selesai dalam 30 hari terakhir
                                            $canReview = \Carbon\Carbon::parse($order->updated_at)->addMonth()->isFuture();
                                        @endphp
                                        <div class="flex justify-between items-start border-b pb-4 last:border-b-0 last:pb-0">
                                            <div class="flex items-start gap-4">
                                                <img src="{{ asset('storage/' . optional($item->product)->main_image) }}" 
                                                     onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';" 
                                                     alt="{{ optional($item->product)->name }}" 
                                                     class="w-16 h-16 bg-gray-200 rounded-md object-cover">
                                                <div>
                                                    <p class="font-semibold">{{ optional($item->product)->name ?? 'Produk Dihapus' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        Varian: {{ optional($item->variant)->color ?? '-' }} / {{ optional($item->variant)->size ?? '-' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">{{ $item->quantity }} x {{ format_rupiah($item->unit_price) }}</p>
                                                </div>
                                            </div>
                                            @if($order->status == 'completed')
                                                <div class="flex-shrink-0">
                                                    @if($canReview)
                                                        @if($existingReview)
                                                            <button class="edit-review-button text-blue-600 font-semibold border border-blue-600 rounded-lg px-4 py-1 text-sm hover:bg-blue-600 hover:text-white transition"
                                                                    data-review-id="{{ $existingReview->id }}">
                                                                Edit Ulasan
                                                            </button>
                                                        @else
                                                            <button class="review-button text-red-600 font-semibold border border-red-600 rounded-lg px-4 py-1 text-sm hover:bg-red-600 hover:text-white transition"
                                                                    data-order-id="{{ $order->id }}"
                                                                    data-product-id="{{ $item->product_id }}"
                                                                    data-product-name="{{ optional($item->product)->name }}"
                                                                    data-product-image="{{ asset('storage/' . optional($item->product)->main_image) }}">
                                                                Beri Ulasan
                                                            </button>
                                                        @endif
                                                    @else
                                                        @if($existingReview)
                                                            <button class="edit-review-button text-blue-600 font-semibold border border-blue-600 rounded-lg px-4 py-1 text-sm hover:bg-blue-600 hover:text-white transition"
                                                                    data-review-id="{{ $existingReview->id }}">
                                                                Lihat Ulasan
                                                            </button>
                                                        @else
                                                            <span class="text-xs text-gray-400 italic">Batas waktu ulasan berakhir</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Footer Order --}}
                                <div class="bg-gray-50 p-4 flex justify-between items-center rounded-b-lg">
                                    <p class="font-semibold">Total Pesanan: <span class="text-red-600">{{ format_rupiah($order->total_price) }}</span></p>
                                    {{-- Memastikan relasi dimuat untuk JSON --}}
                                    <button class="detail-button text-white font-semibold bg-gray-700 rounded-lg px-4 py-1 text-sm hover:bg-gray-800 transition"
                                            data-order-json="{{ json_encode($order->load(['subdomain.user.shop', 'items.product', 'shipping', 'voucher'])) }}">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-500">
                                <p>Anda belum memiliki riwayat pesanan.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginasi -->
                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal untuk Detail Pesanan -->
    <div id="order-detail-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" id="close-detail-modal">&times;</span>
            <h2 class="text-xl font-bold mb-4">Detail Pesanan</h2>
            <div class="text-sm space-y-2 mb-4 border-b pb-4">
                <div class="flex justify-between"><span class="text-gray-500">ID Pesanan:</span><span id="detail-order-id" class="font-semibold font-mono"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal:</span><span id="detail-order-date" class="font-semibold"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Toko:</span><span id="detail-order-shop" class="font-semibold"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status:</span><span id="detail-order-status" class="font-semibold px-2 py-0.5 rounded-full"></span></div>
            </div>
            <div id="detail-item-list" class="space-y-3 mb-4">
                {{-- Item list akan diisi oleh JS --}}
            </div>
            
            {{-- PERUBAHAN 2: Memperbaiki struktur rincian harga di modal --}}
            <div class="border-t pt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal Produk</span><span id="detail-subtotal"></span></div>
                <div id="detail-shipping-row" class="flex justify-between"><span class="text-gray-500">Ongkos Kirim</span><span id="detail-shipping-cost"></span></div>
                
                {{-- Baris ini akan muncul jika ada diskon --}}
                <div id="detail-original-total-row" class="flex justify-between pt-2 mt-2 border-t" style="display: none;">
                    <span class="text-gray-500">Total Asli</span>
                    <span id="detail-original-total" class="line-through"></span>
                </div>
                <div id="detail-discount-row" class="flex justify-between text-green-600" style="display: none;">
                    <span class="text-gray-500">Potongan Voucher</span>
                    <span id="detail-discount"></span>
                </div>
                
                {{-- Total Akhir --}}
                <div class="flex justify-between font-bold text-lg pt-2 mt-2 border-t">
                    <span class="text-gray-800">Total Akhir</span>
                    <span id="detail-total" class="text-red-600"></span>
                </div>
            </div>

            <div id="detail-shipping-info" class="mt-4 border-t pt-4">
                {{-- Info pengiriman/pengambilan akan diisi oleh JS --}}
            </div>
        </div>
    </div>

    <!-- Modal untuk Memberi/Edit Ulasan -->
    <div id="review-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" id="close-review-modal">&times;</span>
            <h2 id="modal-title" class="text-xl font-bold mb-4">Beri Ulasan</h2>
            <div class="flex items-center gap-4 mb-4 border-b pb-4">
                <img id="review-product-image" src="" alt="Product Image" class="w-16 h-16 rounded-md object-cover">
                <p id="review-product-name" class="font-semibold"></p>
            </div>
            <form id="review-form">
                @csrf
                <input type="hidden" name="order_id" id="review-order-id">
                <input type="hidden" name="product_id" id="review-product-id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating Anda</label>
                    <div class="star-rating">
                        <input type="radio" id="5-stars" name="rating" value="5" required /><label for="5-stars">&#9733;</label>
                        <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars">&#9733;</label>
                        <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars">&#9733;</label>
                        <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars">&#9733;</label>
                        <input type="radio" id="1-star" name="rating" value="1" /><label for="1-star">&#9733;</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="review-content" class="block text-sm font-medium text-gray-700">Ulasan Anda</label>
                    <textarea id="review-content" name="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required></textarea>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700">Kirim Ulasan</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Deklarasi Elemen ---
    const reviewModal = document.getElementById('review-modal');
    const detailModal = document.getElementById('order-detail-modal');
    const closeDetailModalBtn = document.getElementById('close-detail-modal');
    const closeReviewModalBtn = document.querySelector('#review-modal .modal-close');
    const reviewForm = document.getElementById('review-form');
    let methodInput = null;
    const starRatingContainer = document.querySelector('.star-rating');
    const stars = starRatingContainer.querySelectorAll('label');
    let currentRating = 0;

    // --- Fungsi Bantuan ---
    const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

    // --- LOGIKA MODAL DETAIL PESANAN ---
    document.querySelectorAll('.detail-button').forEach(button => {
        button.addEventListener('click', function() {
            const order = JSON.parse(this.dataset.orderJson);
            
            document.getElementById('detail-order-id').textContent = order.id;
            document.getElementById('detail-order-date').textContent = new Date(order.order_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('detail-order-shop').textContent = order.subdomain.user.shop.shop_name ?? 'Toko Dihapus';
            
            const statusEl = document.getElementById('detail-order-status');
            let statusText = '', statusClass = '';
            if (order.status == 'completed') { statusText = 'Selesai'; statusClass = 'bg-green-100 text-green-800'; }
            else if (order.status == 'cancelled' || order.status == 'failed') { statusText = 'Dibatalkan'; statusClass = 'bg-red-100 text-red-800'; }
            else { statusText = 'Diproses'; statusClass = 'bg-yellow-100 text-yellow-800'; }
            statusEl.textContent = statusText;
            statusEl.className = `font-semibold px-2 py-0.5 rounded-full ${statusClass}`;

            const itemListEl = document.getElementById('detail-item-list');
            itemListEl.innerHTML = '';
            let subtotal = 0;
            order.items.forEach(item => {
                subtotal += item.quantity * item.unit_price;
                itemListEl.innerHTML += `
                    <div class="flex justify-between text-sm">
                        <div>
                            <p>${item.product ? item.product.name : 'Produk Dihapus'}</p>
                            <p class="text-xs text-gray-500">${item.quantity} x ${formatRupiah(item.unit_price)}</p>
                        </div>
                        <p>${formatRupiah(item.quantity * item.unit_price)}</p>
                    </div>
                `;
            });

            // --- PERUBAHAN 2: Logika untuk menampilkan rincian harga ---
            const shippingCost = order.shipping ? parseFloat(order.shipping.shipping_cost) : 0;
            const originalTotal = subtotal + shippingCost;
            
            // Selalu tampilkan subtotal, ongkir, dan total akhir
            document.getElementById('detail-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('detail-shipping-cost').textContent = formatRupiah(shippingCost);
            document.getElementById('detail-total').textContent = formatRupiah(order.total_price);

            const originalTotalRow = document.getElementById('detail-original-total-row');
            const discountRow = document.getElementById('detail-discount-row');
            
            // Sembunyikan elemen harga tambahan secara default
            originalTotalRow.style.display = 'none';
            discountRow.style.display = 'none';
            document.getElementById('detail-shipping-row').style.display = order.shipping ? 'flex' : 'none';

            // Jika ada voucher, tampilkan rincian harga asli dan potongan
            if (order.voucher) {
                // Gunakan order.discount_amount jika ada, jika tidak hitung manual (lebih aman jika ada dari backend)
                const discountAmount = order.discount_amount || (originalTotal * (order.voucher.discount / 100));

                // Tampilkan harga asli (dicoret)
                document.getElementById('detail-original-total').textContent = formatRupiah(originalTotal);
                originalTotalRow.style.display = 'flex';

                // Tampilkan potongan harga
                document.getElementById('detail-discount').textContent = `- ${formatRupiah(discountAmount)}`;
                discountRow.style.display = 'flex';
            }
            
            const shippingInfoEl = document.getElementById('detail-shipping-info');
            if (order.shipping) {
                shippingInfoEl.innerHTML = `
                    <h4 class="font-semibold text-sm mb-1">Info Pengiriman</h4>
                    <p class="text-xs">${order.shipping.shipping_address}</p>
                    <p class="text-xs text-gray-500 mt-1">${order.shipping.delivery_service}</p>
                `;
            } else {
                shippingInfoEl.innerHTML = `<h4 class="font-semibold text-sm">Metode Pengambilan: Ambil di Toko</h4>`;
            }

            detailModal.style.display = 'block';
        });
    });

    closeDetailModalBtn.onclick = () => detailModal.style.display = 'none';

    // --- LOGIKA MODAL ULASAN (Tidak ada perubahan di sini) ---
    const updateStars = (rating) => {
        currentRating = rating;
        stars.forEach((star, index) => {
            const starValue = 5 - index;
            star.style.color = starValue <= rating ? '#f59e0b' : '#ddd';
        });
    };
    
    stars.forEach(star => {
        star.addEventListener('mouseover', (e) => updateStars(e.target.previousElementSibling.value));
        star.addEventListener('mouseout', () => updateStars(currentRating));
        star.addEventListener('click', (e) => {
            const rating = e.target.previousElementSibling.value;
            document.querySelector(`input[name="rating"][value="${rating}"]`).checked = true;
            updateStars(rating);
        });
    });

    const openModal = (button, isEdit = false, reviewData = null) => {
        reviewForm.reset();
        updateStars(0); 
        if (methodInput) methodInput.remove();

        const modalTitle = document.getElementById('modal-title');
        const submitBtn = reviewForm.querySelector('button[type="submit"]');

        if (isEdit) {
            modalTitle.textContent = 'Edit Ulasan';
            reviewForm.action = "{{ route('review.update', ['testimonial' => '__ID__']) }}".replace('__ID__', reviewData.id);
            
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            reviewForm.prepend(methodInput);

            const productName = reviewData.product ? reviewData.product.name : 'Produk Tidak Ditemukan';
            const productImage = reviewData.product ? "{{ asset('storage/') }}/" + reviewData.product.main_image : 'https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';

            document.getElementById('review-product-name').textContent = productName;
            document.getElementById('review-product-image').src = productImage;
            document.getElementById('review-content').value = reviewData.content;
            
            const rating = reviewData.rating || 0;
            const ratingInput = document.querySelector(`.star-rating input[value="${rating}"]`);
            if (ratingInput) ratingInput.checked = true;
            updateStars(rating);
            
        } else {
            modalTitle.textContent = 'Beri Ulasan';
            reviewForm.action = "{{ route('review.submit') }}";
            
            document.getElementById('review-order-id').value = button.dataset.orderId;
            document.getElementById('review-product-id').value = button.dataset.productId;
            document.getElementById('review-product-name').textContent = button.dataset.productName;
            document.getElementById('review-product-image').src = button.dataset.productImage;
        }
        submitBtn.textContent = isEdit ? 'Simpan Perubahan' : 'Kirim Ulasan';
        reviewModal.style.display = 'block';
    };

    document.querySelectorAll('.review-button').forEach(button => {
        button.addEventListener('click', function() {
            openModal(this, false);
        });
    });

    document.querySelectorAll('.edit-review-button').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            axios.get("{{ url('/review') }}/" + reviewId)
                .then(response => {
                    openModal(this, true, response.data);
                })
                .catch(error => Swal.fire('Oops!', 'Gagal memuat data ulasan.', 'error'));
        });
    });

    closeReviewModalBtn.onclick = () => reviewModal.style.display = 'none';
    window.onclick = (event) => {
        if (event.target == reviewModal || event.target == detailModal) {
            reviewModal.style.display = 'none';
            detailModal.style.display = 'none';
        }
    };

    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';

        const checkedRatingInput = this.querySelector('input[name="rating"]:checked');
        const dataToSend = {
            _token: this.querySelector('input[name="_token"]').value,
            order_id: document.getElementById('review-order-id').value,
            product_id: document.getElementById('review-product-id').value,
            content: document.getElementById('review-content').value,
            rating: checkedRatingInput ? checkedRatingInput.value : null,
        };

        const actionUrl = this.action;
        const method = this.querySelector('input[name="_method"]')?.value || 'POST';

        axios({ 
            method: method, 
            url: actionUrl, 
            data: dataToSend,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            reviewModal.style.display = 'none';
            Swal.fire('Terima Kasih!', response.data.message, 'success')
                .then(() => window.location.reload());
        })
        .catch(error => {
            let message = 'Terjadi kesalahan. Silakan coba lagi.';
            if (error.response && error.response.data) {
                if (error.response.data.errors) {
                    message = Object.values(error.response.data.errors).flat().join('\n');
                } else if (error.response.data.message) {
                    message = error.response.data.message;
                }
            }
            Swal.fire('Oops!', message, 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
});
</script>
@endpush
