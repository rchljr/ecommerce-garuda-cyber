@extends('layouts.mitra')

@section('title', 'Detail Pesanan #' . $order->order_number)

@push('styles')
    {{-- Tidak perlu style khusus lagi setelah JS dihapus --}}
@endpush

@push('scripts')
    {{-- Tidak perlu script khusus lagi setelah JS dihapus --}}
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Detail Pesanan #{{ $order->order_number }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($order->status == 'refund_pending' && $order->refundRequest)
            <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-800 p-6 mb-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-3 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle"></i> Permintaan Pengembalian Dana
                </h2>
                
                {{-- Detail Permintaan Refund --}}
                <div class="bg-white p-4 rounded-md border border-orange-200 space-y-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">Alasan dari Pelanggan:</p>
                        <p class="text-gray-700 italic">"{{ $order->refundRequest->reason }}"</p>
                    </div>
                    <hr>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Metode Pegembalian:</p>
                        <p class="text-gray-700 font-medium">{{ $order->refundRequest->refund_method ?? 'Tidak ditentukan' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Nomor Rekening/Akun:</p>
                        <p class="text-gray-700 font-medium">{{ $order->refundRequest->bank_account_number ?? 'Tidak ditentukan' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Total Refund Diajukan:</p>
                        <p class="text-gray-700 font-bold text-lg">{{ format_rupiah($order->refundRequest->amount ?? 0) }}</p>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                    {{-- Form untuk Menerima Refund --}}
                    <form action="{{ route('mitra.orders.refund.approve', $order->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin MENERIMA permintaan pengembalian dana ini? Status pesanan akan menjadi DANA DIKEMBALIKAN.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full sm:w-auto bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i> Terima Permintaan
                        </button>
                    </form>
                    {{-- Form untuk Menolak Refund --}}
                    <form action="{{ route('mitra.orders.refund.reject', $order->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin MENOLAK permintaan pengembalian dana ini? Status pesanan akan kembali menjadi DIPROSES.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full sm:w-auto bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i> Tolak Permintaan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Ringkasan Pesanan --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Ringkasan Pesanan</h2>
                <div class="space-y-2 text-gray-600">
                    <p><strong>Status:</strong>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($order->status == 'completed') bg-green-100 text-green-800
                            @elseif($order->status == 'pending' || $order->status == 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'shipped' || $order->status == 'ready_for_pickup') bg-blue-100 text-blue-800
                            @elseif($order->status == 'cancelled' || $order->status == 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </p>
                    <p><strong>Tanggal Order:</strong> {{ $order->order_date->format('d F Y H:i') }}</p>
                    <p><strong>Metode Pengiriman:</strong> {{ ucfirst(str_replace('_', ' ', $order->delivery_method ?? 'Tidak Diketahui')) }}</p>
                    <p><strong>Layanan Pengiriman:</strong> {{ $order->shipping->delivery_service ?? 'Belum tersedia' }}</p>
                    <p><strong>Nomor Resi:</strong> {{ $order->shipping->receipt_number ?? 'Belum tersedia' }}</p>
                    <p><strong>Catatan:</strong> {{ $order->notes ?? '-' }}</p>
                    @if ($order->voucher)
                        <p><strong>Voucher Digunakan:</strong> {{ $order->voucher->voucher_code }} (Diskon: {{ $order->voucher->discount }}%)</p>
                    @endif
                    @if ($order->payment)
                        <p><strong>Status Pembayaran:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment->midtrans_transaction_status ?? 'N/A')) }} (Tipe: {{ $order->payment->midtrans_payment_type ?? 'N/A' }})</p>
                    @endif
                </div>
            </div>

            {{-- Detail Pelanggan & Pengiriman --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Detail Pelanggan & Pengiriman</h2>
                <div class="space-y-2 text-gray-600">
                    <p><strong>Nama Pelanggan:</strong> {{ $order->user->name ?? 'Pengguna Dihapus' }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                    <p><strong>Telepon Pengiriman:</strong> {{ $order->shipping_phone }}</p>
                    <p><strong>Alamat Pengiriman:</strong> {{ $order->shipping_address }}, {{ $order->shipping_city }} {{ $order->shipping_zip_code }}</p>
                </div>
            </div>
        </div>

        {{-- Item Pesanan --}}
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Item Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varian</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->variant->name ?? 'N/A' }}
                                    @if($item->variant && $item->variant->options_data)
                                        ({{ collect($item->variant->options_data)->pluck('value')->implode(' / ') }})
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ format_rupiah($item->price) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ format_rupiah($item->price * $item->quantity) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada item dalam pesanan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ringkasan Pembayaran & Update Status --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Ringkasan Pembayaran --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Ringkasan Pembayaran</h2>
                <div class="space-y-2 text-gray-600">
                    <p class="flex justify-between"><span>Subtotal:</span> <span>{{ format_rupiah($order->subtotal) }}</span></p>
                    <p class="flex justify-between"><span>Biaya Pengiriman:</span> <span>{{ format_rupiah($order->shipping_cost) }}</span></p>
                    <p class="flex justify-between"><span>Diskon:</span> <span>- {{ format_rupiah($order->discount_amount) }}</span></p>
                    <p class="flex justify-between text-lg font-bold"><span>Total Pembayaran:</span> <span>{{ format_rupiah($order->total_price) }}</span></p>
                </div>
            </div>

            {{-- Aksi Pesanan (Sudah Diperbaiki dan Disederhanakan) --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Aksi Pesanan</h2>
                
                @if($order->status == 'processing')
                    @if($order->delivery_method == 'ship')
                        {{-- Alur untuk metode DELIVERY --}}
                        <p class="text-sm text-gray-600 mb-3">Masukkan detail pengiriman untuk melanjutkan. Nomor resi wajib diisi.</p>
                        <form action="{{ route('mitra.orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="shipped">
                            <div class="space-y-4">
                                <div>
                                    <label for="delivery-service-input" class="block text-sm font-medium text-gray-700">Layanan Pengiriman</label>
                                    <input type="text" id="delivery-service-input" name="delivery_service" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('delivery_service', $order->shipping->delivery_service ?? '') }}" placeholder="Contoh: JNE REG, SiCepat BEST" required>
                                </div>
                                <div>
                                    <label for="receipt-number-input" class="block text-sm font-medium text-gray-700">Nomor Resi Pengiriman</label>
                                    <input type="text" id="receipt-number-input" name="receipt_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('receipt_number', $order->shipping->receipt_number ?? '') }}" placeholder="Masukkan nomor resi" required>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-truck mr-2"></i> Kirim Pesanan
                                </button>
                            </div>
                        </form>

                    @elseif($order->delivery_method == 'pickup')
                        {{-- Alur untuk metode PICKUP --}}
                        <p class="text-sm text-gray-600 mb-3">Pesanan ini akan diambil langsung oleh pelanggan. Klik tombol di bawah jika pesanan sudah siap.</p>
                        <form action="{{ route('mitra.orders.updateStatus', $order->id) }}" method="POST" onsubmit="return confirm('Anda yakin pesanan ini sudah siap diambil?');">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="ready_for_pickup">
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-box-open mr-2"></i> Tandai Siap Diambil
                            </button>
                        </form>
                    @endif
                
                @elseif(in_array($order->status, ['shipped', 'ready_for_pickup']))
                    <p class="text-sm text-gray-600 mb-3">Menunggu konfirmasi dari pelanggan. Anda dapat menandai pesanan selesai secara manual jika diperlukan.</p>
                    <form action="{{ route('mitra.orders.updateStatus', $order->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyelesaikan pesanan ini secara manual?');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check-circle mr-2"></i> Selesaikan Pesanan
                        </button>
                    </form>

                @else
                    <div class="text-center bg-gray-50 p-6 rounded-lg">
                        <i class="fas fa-info-circle text-2xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Tidak ada aksi yang dapat dilakukan untuk status pesanan saat ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection