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
                                                'completed' => ['text' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                                                'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                                                'failed' => ['text' => 'Gagal', 'class' => 'bg-red-100 text-red-800'],
                                                'default' => ['text' => 'Diproses', 'class' => 'bg-yellow-100 text-yellow-800']
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
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-start gap-4">
                                                <img src="{{ asset('storage/' . optional($item->product)->main_image) }}"
                                                    onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';"
                                                    alt="{{ optional($item->product)->name }}"
                                                    class="w-16 h-16 bg-gray-200 rounded-md object-cover">
                                                <div>
                                                    <p class="font-semibold text-gray-800">
                                                        {{ optional($item->product)->name ?? 'Produk Dihapus' }}</p>
                                                    <p class="text-xs text-gray-500">Varian:
                                                        {{ optional($item->variant)->color ?? '-' }} /
                                                        {{ optional($item->variant)->size ?? '-' }}</p>
                                                    <p class="text-sm text-gray-600">{{ $item->quantity }} x
                                                        {{ format_rupiah($item->unit_price) }}</p>
                                                </div>
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailModal = document.getElementById('order-detail-modal');
            if (!detailModal) return;

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
                        completed: { text: 'Selesai', class: 'bg-green-100 text-green-800' },
                        cancelled: { text: 'Dibatalkan', class: 'bg-red-100 text-red-800' },
                        failed: { text: 'Gagal', class: 'bg-red-100 text-red-800' },
                        default: { text: 'Diproses', class: 'bg-yellow-100 text-yellow-800' }
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
                        itemListEl.innerHTML += `
                                <div class="flex justify-between text-sm">
                                    <div>
                                        <p>${item.product ? item.product.name : 'Produk Dihapus'}</p>
                                        <p class="text-xs text-gray-500">${item.quantity} x ${formatRupiah(item.unit_price)}</p>
                                    </div>
                                    <p>${formatRupiah(itemTotal)}</p>
                                </div>
                            `;
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
                        // === PERUBAHAN 2: AMBIL ALAMAT DARI DATA PENGIRIMAN ===
                        // Alamat ini adalah alamat yang digunakan saat checkout, bukan alamat terbaru customer.
                        const address = order.shipping.shipping_address || 'Alamat tidak diisi saat checkout.';
                        const estimate = order.shipping.estimated_delivery ?? 'Estimasi tidak tersedia';
                        shippingInfoEl.innerHTML = `
                                <h4 class="font-semibold text-sm mb-1">Info Pengiriman</h4>
                                <p class="text-xs">${address}</p>
                                <p class="text-xs text-gray-500 mt-1">${order.shipping.delivery_service || ''}</p>
                                <p class="text-xs text-gray-500 mt-1">Estimasi: ${estimate}</p>
                            `;
                    } else {
                        shippingInfoEl.innerHTML = `<h4 class="font-semibold text-sm">Metode Pengambilan: Ambil di Toko</h4>`;
                    }

                    detailModal.style.display = 'block';
                });
            });

            document.getElementById('close-detail-modal').onclick = () => detailModal.style.display = 'none';
            window.onclick = (event) => {
                if (event.target == detailModal) {
                    detailModal.style.display = 'none';
                }
            };
        });
    </script>
@endpush
