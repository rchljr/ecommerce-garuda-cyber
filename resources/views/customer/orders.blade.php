@extends('layouts.customer')
@section('title', 'Pesanan Saya')

@push('styles')
    {{-- SweetAlert2 for beautiful alerts and confirmations --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- Custom styles for modals and other elements --}}
    <style>
        /* Animation for modal overlay */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        /* Animation for modal content */
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Modal Overlay: The dark background */
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

        /* Modal Content: The actual dialog box */
        .modal-content {
            background-color: #fefefe;
            margin: 8% auto;
            padding: 25px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 0.5rem;
            animation: slideIn 0.4s;
            position: relative;
        }

        /* Close button for modals */
        .modal-close {
            color: #aaa;
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        .modal-close:hover,
        .modal-close:focus {
            color: black;
        }

        /* Star rating styles for the review modal */
        .star-rating {
            display: flex;
            flex-direction: row-reverse; /* This makes stars fill from left to right */
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
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f59e0b; /* amber-500 */
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row gap-8">

        <!-- Left Sidebar -->
        <aside class="w-full md:w-1/4 lg:w-1/5 flex-shrink-0">
            <div class="bg-white p-4 rounded-lg shadow-md">
                @include('layouts._partials.customer-sidebar')
            </div>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4 lg:w-4/5">
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                
                <!-- Header and Search Form -->
                <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Pesanan Saya</h1>
                        <p class="text-gray-500 mt-1">Lacak, ulas, dan lihat riwayat pesanan Anda.</p>
                    </div>
                    <form action="{{ route('tenant.account.orders', ['subdomain' => $subdomain]) }}" method="GET" class="flex-grow md:flex-grow-0">
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama produk atau ID Pesanan..." class="border rounded-lg py-2 pl-10 pr-4 w-full focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Orders List -->
                <div class="space-y-6">
                    @forelse($orders as $order)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            
                            {{-- Order Header --}}
                            <div class="bg-gray-50 p-4 flex flex-wrap justify-between items-center rounded-t-lg border-b gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-gray-800">{{ optional(optional(optional($order->subdomain)->user)->shop)->shop_name ?? 'Toko Dihapus' }}</span>
                                </div>
                                <div>
                                    @php
                                        // Configuration for order status badges
                                        $statusConfig = [
                                            'pending' => ['text' => 'Belum Dibayar', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            'processing' => ['text' => 'Diproses', 'class' => 'bg-blue-100 text-blue-800'],
                                            'shipped' => ['text' => 'Dikirim', 'class' => 'bg-cyan-100 text-cyan-800'],
                                            'ready_for_pickup' => ['text' => 'Siap Diambil', 'class' => 'bg-indigo-100 text-indigo-800'],
                                            'completed' => ['text' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                                            'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                                            'failed' => ['text' => 'Gagal', 'class' => 'bg-red-100 text-red-800'],
                                            'refund_pending' => ['text' => 'Pengajuan Refund', 'class' => 'bg-orange-100 text-orange-800'],
                                            'refunded' => ['text' => 'Dana Dikembalikan', 'class' => 'bg-gray-100 text-gray-800'],
                                        ];
                                        $status = $statusConfig[$order->status] ?? $statusConfig['processing'];
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $status['class'] }}">
                                        {{ $status['text'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- Order Body (Items) --}}
                            <div class="p-4 space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex justify-between items-start border-b pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex items-start gap-4 flex-grow">
                                            @php
                                                $productName = optional($item->product)->name ?? 'Produk Dihapus';
                                                $imageUrl = optional($item->product)->main_image 
                                                    ? asset('storage/' . $item->product->main_image) 
                                                    : 'https://placehold.co/64x64/f1f5f9/64748b?text=' . urlencode(substr($productName, 0, 15));
                                            @endphp
                                            <img src="{{ $imageUrl }}" 
                                                alt="{{ $productName }}" 
                                                class="w-16 h-16 bg-gray-200 rounded-md object-cover"
                                                onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/64748b?text={{$productName}}';">

                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $productName }}</p>
                                                <p class="text-xs text-gray-500">Varian: {{ optional($item->variant)->name ?? '-' }}</p>
                                                <p class="text-sm text-gray-600">{{ $item->quantity }} x {{ format_rupiah($item->price) }}</p>
                                            </div>
                                        </div>
                                        
                                        {{-- Review Buttons --}}
                                        <div class="flex-shrink-0 ml-4">
                                            @if($order->status == 'completed' && $item->product)
                                                @php
                                                    $testimonial = $order->testimonials->where('product_id', $item->product_id)->where('user_id', Auth::guard('customers')->id())->first();
                                                @endphp
                                                @if($testimonial)
                                                    <button class="edit-review-btn text-sm font-semibold text-blue-600 hover:underline" data-testimonial-id="{{ $testimonial->id }}">Edit Ulasan</button>
                                                @else
                                                    <button class="give-review-btn text-sm font-semibold text-green-600 hover:underline" data-order-id="{{ $order->id }}" data-product-id="{{ $item->product_id }}">Beri Ulasan</button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Order Footer (Total & Action Buttons) --}}
                            <div class="bg-gray-50 p-4 flex flex-wrap gap-4 justify-between items-center rounded-b-lg">
                                <div>
                                    <span class="text-sm text-gray-600">Total Pesanan:</span>
                                    <span class="font-bold text-lg text-red-600">{{ format_rupiah($order->total_price) }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    @php
                                        $finalStatuses = ['completed', 'cancelled', 'failed', 'refunded'];
                                    @endphp
                                    @if(in_array($order->status, $finalStatuses))
                                        <button 
                                            class="buy-again-btn text-white font-semibold bg-blue-600 rounded-lg px-5 py-2 text-sm hover:bg-blue-700 transition"
                                            data-items="{{ json_encode($order->items->map(function($item) {
                                                if ($item->variant) { // Pastikan varian masih ada
                                                    return ['variant_id' => $item->product_variant_id, 'quantity' => $item->quantity];
                                                }
                                                return null;
                                            })->filter()) }}">
                                            Beli Lagi
                                        </button>
                                    @endif
                                    
                                    @if($order->status == 'pending')
                                        <button class="cancel-order-btn text-white font-semibold bg-red-600 rounded-lg px-5 py-2 text-sm hover:bg-red-700 transition" data-order-id="{{ $order->id }}">Batalkan Pesanan</button>
                                    @endif
                                    @if($order->status == 'processing' && !$order->refundRequest)
                                        <button class="request-refund-btn text-white font-semibold bg-orange-500 rounded-lg px-5 py-2 text-sm hover:bg-orange-600 transition" data-order-id="{{ $order->id }}" data-total-price="{{ $order->total_price }}">Ajukan Refund</button>
                                    @endif
                                    @if(in_array($order->status, ['shipped', 'ready_for_pickup']))
                                        <button class="receive-order-btn text-white font-semibold bg-green-600 rounded-lg px-5 py-2 text-sm hover:bg-green-700 transition" data-order-id="{{ $order->id }}">Pesanan Diterima</button>
                                    @endif
                                    <button class="detail-button text-white font-semibold bg-gray-800 rounded-lg px-5 py-2 text-sm hover:bg-black transition" data-order-json="{{ json_encode($order) }}">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 text-gray-500 border-2 border-dashed rounded-lg">
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                @if($search)
                                    Pesanan tidak ditemukan
                                @else
                                    Belum ada pesanan
                                @endif
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($search)
                                    Coba kata kunci lain atau <a href="{{ route('tenant.account.orders', ['subdomain' => $subdomain]) }}" class="text-red-600 hover:underline">lihat semua pesanan</a>.
                                @else
                                    Mulai belanja sekarang untuk melihat pesanan Anda di sini.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </main>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODALS: All modals are placed here at the end of the main content -->
<!-- ======================================================================= -->

<!-- Order Detail Modal -->
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
        <div id="detail-item-list" class="space-y-3 mb-4"></div>
        <div class="border-t pt-4 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Subtotal Produk</span><span id="detail-subtotal"></span></div>
            <div id="detail-shipping-row" class="flex justify-between"><span class="text-gray-500">Ongkos Kirim</span><span id="detail-shipping-cost"></span></div>
            <div id="detail-discount-row" class="flex justify-between text-green-600" style="display: none;"><span class="text-gray-500">Potongan Voucher</span><span id="detail-discount"></span></div>
            <div class="flex justify-between font-bold text-lg pt-2 mt-2 border-t"><span class="text-gray-800">Total Akhir</span><span id="detail-total" class="text-red-600"></span></div>
        </div>
        <div id="detail-shipping-info" class="mt-4 border-t pt-4"></div>
        <div id="detail-notes-info" class="mt-4 border-t pt-4" style="display: none;">
            <h4 class="font-semibold text-sm mb-1">Catatan Pesanan:</h4>
            <p id="detail-notes-text" class="text-sm text-gray-600 bg-gray-50 p-3 rounded-md"></p>
        </div>
    </div>
</div>

<!-- Refund Request Modal -->
<div id="refund-modal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close" id="close-refund-modal">&times;</span>
        <h2 class="text-xl font-bold mb-4">Formulir Pengajuan Refund</h2>
        <form id="refund-form">
            @csrf
            {{-- PERBAIKAN 2: Modifikasi Modal Refund --}}
            <div class="mb-4">
                <label for="refund-total" class="block text-gray-700 text-sm font-bold mb-2">Total Pengembalian Dana</label>
                <input type="text" id="refund-total" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-200" readonly>
            </div>
            <div class="mb-4">
                <label for="refund-method" class="block text-gray-700 text-sm font-bold mb-2">Metode Pengembalian</label>
                <select id="refund-method" name="refund_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="bca">Transfer Bank - BCA</option>
                    <option value="bri">Transfer Bank - BRI</option>
                    <option value="gopay">E-Wallet - Gopay</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="refund-account-number" class="block text-gray-700 text-sm font-bold mb-2">Nomor Rekening / E-Wallet</label>
                <input type="text" id="refund-account-number" name="bank_account_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: 1234567890" required>
            </div>
            <div class="mb-6">
                <label for="refund-reason" class="block text-gray-700 text-sm font-bold mb-2">Alasan Pengembalian</label>
                <textarea id="refund-reason" name="reason" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Jelaskan alasan Anda mengajukan pengembalian dana..." required minlength="10"></textarea>
            </div>
            <div class="flex items-center justify-end">
                <button type="submit" id="submit-refund-button" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition">Ajukan Refund</button>
            </div>
        </form>
    </div>
</div>

<!-- Product Review Modal -->
<div id="review-modal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close" id="close-review-modal">&times;</span>
        <h2 id="review-modal-title" class="text-xl font-bold mb-4">Beri Ulasan Produk</h2>
        <form id="review-form">
            @csrf
            <input type="hidden" id="review-order-id" name="order_id">
            <input type="hidden" id="review-product-id" name="product_id">
            <input type="hidden" id="review-method" name="_method" value="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Rating Anda:</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars">★</label>
                    <input type="radio" id="star4" name="rating" value="4"/><label for="star4" title="4 stars">★</label>
                    <input type="radio" id="star3" name="rating" value="3"/><label for="star3" title="3 stars">★</label>
                    <input type="radio" id="star2" name="rating" value="2"/><label for="star2" title="2 stars">★</label>
                    <input type="radio" id="star1" name="rating" value="1"/><label for="star1" title="1 star">★</label>
                </div>
            </div>
            <div class="mb-6">
                <label for="review-content" class="block text-gray-700 text-sm font-bold mb-2">Ulasan Anda:</label>
                <textarea id="review-content" name="content" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Bagaimana pengalaman Anda dengan produk ini?" required minlength="5"></textarea>
            </div>
            <div class="flex items-center justify-end">
                <button type="submit" id="submit-review-button" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition">
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
    // --- Global Variables & Helpers ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const currentSubdomain = "{{ $subdomain }}";

    const formatRupiah = (number) => {
        const num = parseFloat(number);
        if (isNaN(num)) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
    };

    // --- Unified Modal Closing Logic ---
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.onclick = () => btn.closest('.modal-overlay').style.display = 'none';
    });
    window.onclick = (event) => {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.style.display = 'none';
        }
    };

    // --- Order Detail Modal Logic ---
    document.querySelectorAll('.detail-button').forEach(button => {
        button.addEventListener('click', function () {
            const modal = document.getElementById('order-detail-modal');
            const order = JSON.parse(this.dataset.orderJson);
            
            // Basic Info
            modal.querySelector('#detail-order-id').textContent = order.order_number;
            modal.querySelector('#detail-order-date').textContent = new Date(order.order_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            modal.querySelector('#detail-order-shop').textContent = order.subdomain?.user?.shop?.shop_name ?? 'Toko Dihapus';

            // Status Badge
            const statusEl = modal.querySelector('#detail-order-status');
            const statusConfig = {
                pending: { text: 'Belum Dibayar', class: 'bg-yellow-100 text-yellow-800' },
                processing: { text: 'Diproses', class: 'bg-blue-100 text-blue-800' },
                shipped: { text: 'Dikirim', class: 'bg-cyan-100 text-cyan-800' },
                ready_for_pickup: { text: 'Siap Diambil', class: 'bg-indigo-100 text-indigo-800' },
                completed: { text: 'Selesai', class: 'bg-green-100 text-green-800' },
                cancelled: { text: 'Dibatalkan', class: 'bg-red-100 text-red-800' },
                failed: { text: 'Gagal', class: 'bg-red-100 text-red-800' },
                refund_pending: { text: 'Pengajuan Refund', class: 'bg-orange-100 text-orange-800' },
                refunded: { text: 'Dana Dikembalikan', class: 'bg-gray-100 text-gray-800' },
            };
            const status = statusConfig[order.status] || statusConfig.processing;
            statusEl.textContent = status.text;
            statusEl.className = `font-semibold px-2 py-0.5 rounded-full text-xs ${status.class}`;

            // Item List
            const itemListEl = modal.querySelector('#detail-item-list');
            itemListEl.innerHTML = '';
            order.items.forEach(item => {
                itemListEl.innerHTML += `
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="font-medium">${item.product ? item.product.name : 'Produk Dihapus'}</p>
                            <p class="text-xs text-gray-500">${item.quantity} x ${formatRupiah(item.price)}</p>
                        </div>
                        <p>${formatRupiah(item.quantity * item.price)}</p>
                    </div>`;
            });

            // Financials
            modal.querySelector('#detail-subtotal').textContent = formatRupiah(order.subtotal);
            modal.querySelector('#detail-shipping-cost').textContent = formatRupiah(order.shipping_cost);
            modal.querySelector('#detail-total').textContent = formatRupiah(order.total_price);
            
            const discountRow = modal.querySelector('#detail-discount-row');
            if (parseFloat(order.discount_amount) > 0) {
                modal.querySelector('#detail-discount').textContent = `- ${formatRupiah(order.discount_amount)}`;
                discountRow.style.display = 'flex';
            } else {
                discountRow.style.display = 'none';
            }

            // Shipping Info & Receipt Number Logic
            const shippingInfoEl = modal.querySelector('#detail-shipping-info');
            if (order.delivery_method === 'ship') {
                // Handle null or empty string for courier
                const courier = order.shipping?.delivery_service ? order.shipping.delivery_service.toUpperCase() : 'N/A';
                let shippingHtml = `<h4 class="font-semibold text-sm mb-1">Info Pengiriman</h4>`;
                shippingHtml += `<p class="text-sm text-gray-700">${order.shipping_address || 'Alamat tidak tersedia'}</p>`;
                shippingHtml += `<p class="text-xs text-gray-500 mt-1">Kurir: ${courier}</p>`;
                
                // Show receipt number only if status is shipped or later
                if (['shipped', 'ready_for_pickup', 'completed'].includes(order.status) && order.shipping?.receipt_number) {
                    shippingHtml += `<p class="text-xs text-gray-500">No. Resi: <strong class="font-mono">${order.shipping.receipt_number}</strong></p>`;
                }
                shippingInfoEl.innerHTML = shippingHtml;
            } else {
                shippingInfoEl.innerHTML = `<h4 class="font-semibold text-sm">Metode Pengambilan: Ambil di Toko</h4>`;
            }

            // Notes
            const notesInfoEl = modal.querySelector('#detail-notes-info');
            if (order.notes && order.notes.trim() !== '') {
                modal.querySelector('#detail-notes-text').textContent = order.notes;
                notesInfoEl.style.display = 'block';
            } else {
                notesInfoEl.style.display = 'none';
            }

            modal.style.display = 'block';
        });
    });

    // --- Action Button Logic ---
    // 1. Cancel Order
    document.querySelectorAll('.cancel-order-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            Swal.fire({
                title: 'Anda yakin?',
                text: "Pesanan yang sudah dibatalkan tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/tenant/${currentSubdomain}/account/orders/${orderId}/cancel`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    }).then(res => res.json().then(data => ({ok: res.ok, data})))
                    .then(({ok, data}) => {
                        if (ok) {
                            Swal.fire('Dibatalkan!', data.message, 'success').then(() => window.location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    }).catch(() => Swal.fire('Error!', 'Tidak dapat menghubungi server.', 'error'));
                }
            });
        });
    });

    // 2. Receive Order
    document.querySelectorAll('.receive-order-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            Swal.fire({
                title: 'Konfirmasi Penerimaan',
                text: "Pastikan Anda sudah menerima produk dalam kondisi baik.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, sudah diterima!',
                cancelButtonText: 'Belum'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/tenant/${currentSubdomain}/account/orders/${orderId}/receive`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    }).then(res => res.json().then(data => ({ok: res.ok, data})))
                    .then(({ok, data}) => {
                        if (ok) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    }).catch(() => Swal.fire('Error!', 'Tidak dapat menghubungi server.', 'error'));
                }
            });
        });
    });

    // 3. Refund Request
    const refundModal = document.getElementById('refund-modal');
    const refundForm = document.getElementById('refund-form');
    let currentRefundOrderId = null;

    document.querySelectorAll('.request-refund-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentRefundOrderId = this.dataset.orderId;
            const totalPrice = this.dataset.totalPrice;
            // PERBAIKAN: Pindahkan reset() sebelum mengisi nilai
            refundForm.reset(); 
            refundModal.querySelector('#refund-total').value = formatRupiah(totalPrice);
            refundModal.style.display = 'block';
        });
    });

    refundForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitButton = refundModal.querySelector('#submit-refund-button');
        submitButton.disabled = true;
        submitButton.textContent = 'Mengirim...';

        fetch(`/tenant/${currentSubdomain}/account/orders/${currentRefundOrderId}/request-refund`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: new FormData(this)
        })
        .then(res => res.json().then(data => ({ok: res.ok, data})))
        .then(({ok, data}) => {
            if (ok) {
                refundModal.style.display = 'none';
                Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload());
            } else {
                const errorMessage = data.errors ? Object.values(data.errors).join('\n') : data.message;
                Swal.fire('Gagal!', errorMessage || 'Terjadi kesalahan.', 'error');
            }
        })
        .catch(() => Swal.fire('Error!', 'Tidak dapat menghubungi server.', 'error'))
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Ajukan Refund';
        });
    });

    // 4. Review Modal Logic (Create & Edit)
    const reviewModal = document.getElementById('review-modal');
    const reviewForm = document.getElementById('review-form');
    
    // Open modal for "Beri Ulasan"
    document.querySelectorAll('.give-review-btn').forEach(button => {
        button.addEventListener('click', function () {
            reviewForm.reset();
            reviewModal.querySelectorAll('.star-rating input').forEach(input => input.checked = false);
            reviewModal.querySelector('#review-modal-title').textContent = 'Beri Ulasan Produk';
            reviewModal.querySelector('#review-order-id').value = this.dataset.orderId;
            reviewModal.querySelector('#review-product-id').value = this.dataset.productId;
            reviewModal.querySelector('#review-method').value = 'POST';
            reviewForm.action = `{{ route('tenant.customer.reviews.submit', ['subdomain' => $subdomain]) }}`;
            reviewModal.style.display = 'block';
        });
    });

    // Open modal for "Edit Ulasan"
    document.querySelectorAll('.edit-review-btn').forEach(button => {
        button.addEventListener('click', function () {
            const testimonialId = this.dataset.testimonialId;
            const url = `{{ route('tenant.customer.reviews.json', ['subdomain' => $subdomain, 'testimonial' => '__ID__']) }}`.replace('__ID__', testimonialId);

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil data ulasan.');
                    return response.json();
                })
                .then(data => {
                    reviewForm.reset();
                    reviewModal.querySelector('#review-modal-title').textContent = 'Edit Ulasan Anda';
                    reviewModal.querySelector('#review-order-id').value = data.order_id;
                    reviewModal.querySelector('#review-product-id').value = data.product_id;
                    reviewModal.querySelector('#review-content').value = data.content;
                    reviewModal.querySelector('#review-method').value = 'PUT';
                    
                    const starInput = reviewModal.querySelector(`.star-rating input[value="${data.rating}"]`);
                    if (starInput) starInput.checked = true;

                    reviewForm.action = `{{ route('tenant.customer.reviews.update', ['subdomain' => $subdomain, 'testimonial' => '__ID__']) }}`.replace('__ID__', data.id);
                    reviewModal.style.display = 'block';
                })
                .catch(error => Swal.fire('Error', error.message, 'error'));
        });
    });

    // 5. Buy Again Button Logic ==
    document.querySelectorAll('.buy-again-btn').forEach(button => {
        button.addEventListener('click', function() {
            const items = JSON.parse(this.dataset.items);
            
            if (!items || items.length === 0) {
                Swal.fire('Gagal', 'Produk dari pesanan ini sudah tidak tersedia.', 'error');
                return;
            }

            Swal.fire({
                title: 'Menambahkan ke Keranjang...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/tenant/${currentSubdomain}/cart/add-multiple`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ items: items })
            })
            .then(res => res.json().then(data => ({ok: res.ok, data})))
            .then(({ok, data}) => {
                if (ok) {
                    // Update cart count in header
                    const cartCountElement = document.getElementById('cart-count-badge');
                    if(cartCountElement && data.cart_count) {
                        cartCountElement.textContent = data.cart_count;
                        cartCountElement.classList.remove('hidden');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showCancelButton: true,
                        confirmButtonText: 'Lihat Keranjang',
                        cancelButtonText: 'Lanjut Belanja',
                        confirmButtonColor: '#10B981', // emerald-500
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `{{ route('tenant.cart.index', ['subdomain' => $subdomain]) }}`;
                        }
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Beberapa produk mungkin sudah tidak tersedia.', 'error');
                }
            })
            .catch(() => Swal.fire('Error!', 'Tidak dapat menghubungi server. Silakan coba lagi.', 'error'));
        });
    });

    // Handle Review Form Submission (for both Create and Update)
    reviewForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const submitButton = reviewModal.querySelector('#submit-review-button');
        submitButton.disabled = true;
        submitButton.textContent = 'Mengirim...';
        
        fetch(this.action, {
            method: 'POST', // Always POST, Laravel handles PUT/PATCH via _method field
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: new FormData(this)
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok) {
                reviewModal.style.display = 'none';
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message })
                    .then(() => window.location.reload());
            } else {
                const errorMessage = data.errors ? Object.values(data.errors).join('\n') : data.message;
                throw new Error(errorMessage || 'Terjadi kesalahan.');
            }
        })
        .catch(error => Swal.fire('Error', error.message, 'error'))
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Kirim Ulasan';
        });
    });
});
</script>
@endpush