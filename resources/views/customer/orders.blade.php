@extends('layouts.customer')
@section('title', 'Pesanan Saya')

@push('styles')
    {{-- PERBAIKAN: Memastikan SweetAlert2 dimuat --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background-color: rgba(0, 0, 0, 0.6);
            animation: fadeIn 0.4s;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 8% auto;
            padding: 25px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 0.5rem;
            animation: slideIn 0.4s;
        }

        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Gaya untuk rating bintang di modal */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f59e0b;
            /* amber-500 */
        }
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
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Pesanan Saya</h1>
                    <p class="text-gray-500 mt-1 mb-6">Lacak, ulas, dan lihat riwayat pesanan Anda.</p>

                    <!-- Daftar Pesanan -->
                    <div class="space-y-6">
                        @forelse($orders as $order)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                {{-- Header Order --}}
                                <div class="bg-gray-50 p-4 flex flex-wrap justify-between items-center rounded-t-lg border-b">
                                    <div class="flex items-center gap-3">
                                        @if($order->subdomain && optional($order->subdomain->user)->shop)
                                            <a href="{{ route('tenant.home', ['subdomain' => $order->subdomain->subdomain_name]) }}"
                                                class="font-semibold text-gray-800 hover:text-red-600 transition">
                                                {{ $order->subdomain->user->shop->shop_name }}
                                            </a>
                                        @else
                                            <span class="font-semibold text-gray-500 italic">Toko Dihapus</span>
                                        @endif
                                    </div>
                                    <div>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['text' => 'Belum Dibayar', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'completed' => ['text' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                                                'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                                                'failed' => ['text' => 'Gagal', 'class' => 'bg-red-100 text-red-800'],
                                                'default' => ['text' => 'Diproses', 'class' => 'bg-blue-100 text-blue-800']
                                            ];
                                            $status = $statusConfig[$order->status] ?? $statusConfig['default'];
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $status['class'] }}">
                                            {{ $status['text'] }}
                                        </span>
                                    </div>
                                </div>
                                {{-- Body Order (Item) --}}
                                <div class="p-4 space-y-4">
                                    @foreach($order->items as $item)
                                        <div class="flex justify-between items-start border-b pb-4 last:border-b-0 last:pb-0">
                                            <div class="flex items-start gap-4 flex-grow">
                                                <img src="{{ asset('storage/' . optional($item->product)->main_image) }}"
                                                    onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';"
                                                    alt="{{ optional($item->product)->name }}"
                                                    class="w-16 h-16 bg-gray-200 rounded-md object-cover">
                                                <div>
                                                    <p class="font-semibold text-gray-800">
                                                        {{ optional($item->product)->name ?? 'Produk Dihapus' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">Varian:
                                                        {{ optional($item->variant)->color ?? '-' }} /
                                                        {{ optional($item->variant)->size ?? '-' }}
                                                    </p>
                                                    <p class="text-sm text-gray-600">{{ $item->quantity }} x
                                                        {{ format_rupiah($item->unit_price) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ml-4">
                                                @if($order->status == 'completed' && \Carbon\Carbon::parse($order->order_date)->addMonths(3)->isFuture())
                                                    @php
                                                        $testimonial = $order->testimonials->firstWhere('product_id', $item->product_id);
                                                    @endphp
                                                    @if($testimonial)
                                                        <button class="edit-review-btn text-sm font-semibold text-blue-600 hover:underline"
                                                            data-testimonial-id="{{ $testimonial->id }}">
                                                            Edit Ulasan
                                                        </button>
                                                    @else
                                                        <button class="give-review-btn text-sm font-semibold text-green-600 hover:underline"
                                                            data-order-id="{{ $order->id }}" data-product-id="{{ $item->product_id }}">
                                                            Beri Ulasan
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Footer Order --}}
                                <div class="bg-gray-50 p-4 flex flex-wrap gap-4 justify-between items-center rounded-b-lg">
                                    <div>
                                        <span class="text-sm text-gray-600">Total Pesanan:</span>
                                        <span
                                            class="font-bold text-lg text-red-600">{{ format_rupiah($order->total_price) }}</span>
                                    </div>
                                    <button
                                        class="detail-button text-white font-semibold bg-gray-800 rounded-lg px-5 py-2 text-sm hover:bg-black transition flex items-center gap-2"
                                        data-order-json="{{ json_encode($order) }}">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16 text-gray-500 border-2 border-dashed rounded-lg">
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pesanan</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai belanja sekarang untuk melihat pesanan Anda di sini.
                                </p>
                            </div>
                        @endforelse
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
                <div class="flex justify-between"><span class="text-gray-500">ID Pesanan:</span><span id="detail-order-id"
                        class="font-semibold font-mono"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal:</span><span id="detail-order-date"
                        class="font-semibold"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Toko:</span><span id="detail-order-shop"
                        class="font-semibold"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status:</span><span id="detail-order-status"
                        class="font-semibold px-2 py-0.5 rounded-full"></span></div>
            </div>
            <div id="detail-item-list" class="space-y-3 mb-4"></div>
            <div class="border-t pt-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal Produk</span><span
                        id="detail-subtotal"></span></div>
                <div id="detail-shipping-row" class="flex justify-between"><span class="text-gray-500">Ongkos
                        Kirim</span><span id="detail-shipping-cost"></span></div>
                <div id="detail-discount-row" class="flex justify-between text-green-600" style="display: none;"><span
                        class="text-gray-500">Potongan Voucher</span><span id="detail-discount"></span></div>
                <div class="flex justify-between font-bold text-lg pt-2 mt-2 border-t"><span class="text-gray-800">Total
                        Akhir</span><span id="detail-total" class="text-red-600"></span></div>
            </div>
            <div id="detail-shipping-info" class="mt-4 border-t pt-4"></div>
            <div id="detail-notes-info" class="mt-4 border-t pt-4" style="display: none;">
                <h4 class="font-semibold text-sm mb-1">Catatan:</h4>
                <p id="detail-notes-text" class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md"></p>
            </div>
        </div>
    </div>

    <!-- Modal untuk Ulasan Produk -->
    <div id="review-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" id="close-review-modal">&times;</span>
            <h2 id="review-modal-title" class="text-xl font-bold mb-4">Beri Ulasan Produk</h2>
            <form id="review-form">
                @csrf
                <input type="hidden" id="review-order-id" name="order_id">
                <input type="hidden" id="review-product-id" name="product_id">
                <input type="hidden" id="review-method" name="_method" value="POST">
                <input type="hidden" id="review-testimonial-id" name="testimonial_id">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rating Anda:</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" /><label for="star5"
                            title="5 stars">★</label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4"
                            title="4 stars">★</label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3"
                            title="3 stars">★</label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2"
                            title="2 stars">★</label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="review-content" class="block text-gray-700 text-sm font-bold mb-2">Ulasan Anda:</label>
                    <textarea id="review-content" name="content" rows="4"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Bagaimana pengalaman Anda dengan produk ini?"></textarea>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" id="submit-review-button"
                        class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailModal = document.getElementById('order-detail-modal');
            const reviewModal = document.getElementById('review-modal');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const currentSubdomain = "{{ $subdomain }}";

            // ... (Kode untuk modal detail tetap sama) ...
            const formatRupiah = (number) => {
                const num = parseFloat(number);
                if (isNaN(num)) return 'Rp 0';
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
            };

            document.querySelectorAll('.detail-button').forEach(button => {
                button.addEventListener('click', function () {
                    const order = JSON.parse(this.dataset.orderJson);
                    document.getElementById('detail-order-id').textContent = order.id;
                    document.getElementById('detail-order-date').textContent = new Date(order.order_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    document.getElementById('detail-order-shop').textContent = order.subdomain?.user?.shop?.shop_name ?? 'Toko Dihapus';
                    const statusEl = document.getElementById('detail-order-status');
                    const statusConfig = {
                        pending: { text: 'Belum Dibayar', class: 'bg-yellow-100 text-yellow-800' },
                        completed: { text: 'Selesai', class: 'bg-green-100 text-green-800' },
                        cancelled: { text: 'Dibatalkan', class: 'bg-red-100 text-red-800' },
                        failed: { text: 'Gagal', class: 'bg-red-100 text-red-800' },
                        default: { text: 'Diproses', class: 'bg-blue-100 text-blue-800' }
                    };
                    const status = statusConfig[order.status] || statusConfig.default;
                    statusEl.textContent = status.text;
                    statusEl.className = `font-semibold px-2 py-0.5 rounded-full ${status.class}`;
                    const itemListEl = document.getElementById('detail-item-list');
                    itemListEl.innerHTML = '';
                    let calculatedSubtotal = 0;
                    order.items.forEach(item => {
                        const itemTotal = (item.quantity || 0) * (item.unit_price || 0);
                        calculatedSubtotal += itemTotal;
                        itemListEl.innerHTML += `<div class="flex justify-between text-sm"><div><p>${item.product ? item.product.name : 'Produk Dihapus'}</p><p class="text-xs text-gray-500">${item.quantity} x ${formatRupiah(item.unit_price)}</p></div><p>${formatRupiah(itemTotal)}</p></div>`;
                    });
                    const subtotal = parseFloat(order.subtotal) > 0 ? parseFloat(order.subtotal) : calculatedSubtotal;
                    const shippingCost = parseFloat(order.shipping_cost) || 0;
                    const discountAmount = parseFloat(order.discount_amount) || 0;
                    const correctFinalTotal = subtotal + shippingCost - discountAmount;
                    document.getElementById('detail-subtotal').textContent = formatRupiah(subtotal);
                    document.getElementById('detail-shipping-cost').textContent = formatRupiah(shippingCost);
                    document.getElementById('detail-total').textContent = formatRupiah(correctFinalTotal);
                    const discountRow = document.getElementById('detail-discount-row');
                    const shippingRow = document.getElementById('detail-shipping-row');
                    shippingRow.style.display = order.shipping ? 'flex' : 'none';
                    if (discountAmount > 0) {
                        document.getElementById('detail-discount').textContent = `- ${formatRupiah(discountAmount)}`;
                        discountRow.style.display = 'flex';
                    } else {
                        discountRow.style.display = 'none';
                    }
                    const shippingInfoEl = document.getElementById('detail-shipping-info');
                    if (order.shipping) {
                        shippingInfoEl.innerHTML = `<h4 class="font-semibold text-sm mb-1">Info Pengiriman</h4><p class="text-xs text-gray-500 mt-1">${order.shipping.delivery_service || ''}</p>`;
                    } else {
                        shippingInfoEl.innerHTML = `<h4 class="font-semibold text-sm">Metode Pengambilan: Ambil di Toko</h4>`;
                    }
                    const notesInfoEl = document.getElementById('detail-notes-info');
                    if (order.notes && order.notes.trim() !== '') {
                        document.getElementById('detail-notes-text').textContent = order.notes;
                        notesInfoEl.style.display = 'block';
                    } else {
                        notesInfoEl.style.display = 'none';
                    }
                    detailModal.style.display = 'block';
                });
            });
            document.getElementById('close-detail-modal').onclick = () => detailModal.style.display = 'none';
            window.onclick = (event) => {
                if (event.target == detailModal) detailModal.style.display = 'none';
                if (event.target == reviewModal) reviewModal.style.display = 'none';
            };

            // --- Logika untuk Modal Ulasan ---
            const reviewForm = document.getElementById('review-form');
            const reviewModalTitle = document.getElementById('review-modal-title');
            const reviewOrderId = document.getElementById('review-order-id');
            const reviewProductId = document.getElementById('review-product-id');
            const reviewMethod = document.getElementById('review-method');
            const reviewTestimonialId = document.getElementById('review-testimonial-id');
            const reviewContent = document.getElementById('review-content');
            const starInputs = document.querySelectorAll('.star-rating input');

            function openReviewModal() {
                reviewModal.style.display = 'block';
            }
            document.getElementById('close-review-modal').onclick = () => reviewModal.style.display = 'none';

            // Event untuk tombol "Beri Ulasan"
            document.querySelectorAll('.give-review-btn').forEach(button => {
                button.addEventListener('click', function () {
                    reviewForm.reset();
                    reviewModalTitle.textContent = 'Beri Ulasan Produk';
                    reviewOrderId.value = this.dataset.orderId;
                    reviewProductId.value = this.dataset.productId;
                    reviewMethod.value = 'POST';
                    reviewTestimonialId.value = ''; // Kosongkan ID testimoni
                    reviewForm.action = `{{ route('tenant.customer.reviews.submit', ['subdomain' => $subdomain]) }}`;
                    openReviewModal();
                });
            });

            // Event untuk tombol "Edit Ulasan"
            document.querySelectorAll('.edit-review-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const testimonialId = this.dataset.testimonialId;
                    const url = `{{ route('tenant.customer.reviews.json', ['subdomain' => $subdomain, 'testimonial' => '__ID__']) }}`.replace('__ID__', testimonialId);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Gagal mengambil data ulasan.');
                            }
                            return response.json();
                        })
                        .then(data => {
                            reviewForm.reset();
                            reviewModalTitle.textContent = 'Edit Ulasan Anda';
                            reviewOrderId.value = data.order_id;
                            reviewProductId.value = data.product_id;
                            reviewMethod.value = 'PUT';
                            reviewTestimonialId.value = data.id;
                            reviewContent.value = data.content;

                            const starInput = document.querySelector(`.star-rating input[value="${data.rating}"]`);
                            if (starInput) {
                                starInput.checked = true;
                            }

                            reviewForm.action = `{{ route('tenant.customer.reviews.update', ['subdomain' => $subdomain, 'testimonial' => '__ID__']) }}`.replace('__ID__', data.id);
                            openReviewModal();
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message, 'error');
                        });
                });
            });

            // Submit form ulasan
            reviewForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const submitButton = document.getElementById('submit-review-button');
                submitButton.disabled = true;
                submitButton.textContent = 'Mengirim...';

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST', // Selalu POST, karena _method akan di-handle Laravel
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                    .then(response => response.json().then(data => ({ ok: response.ok, data })))
                    .then(({ ok, data }) => {
                        if (ok) {
                            reviewModal.style.display = 'none';
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan.');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', error.message, 'error');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Kirim Ulasan';
                    });
            });
        });
    </script>
@endpush