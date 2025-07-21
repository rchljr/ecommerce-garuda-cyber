@extends('layouts.mitra')

@section('title', 'Detail Pesanan')

@section('head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Google: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        /* Styling untuk tabel yang lebih baik */
        .table-header th {
            background-color: #f9fafb; /* Light gray for header */
            color: #4b5563; /* Darker gray for text */
            font-weight: 600;
            padding: 12px 16px;
            text-transform: uppercase;
            font-size: 0.75rem; /* text-xs */
            letter-spacing: 0.05em; /* tracking-wider */
            border-bottom: 1px solid #e5e7eb;
        }
        .table-body td {
            padding: 12px 16px;
            font-size: 0.875rem; /* text-sm */
            color: #374151; /* Dark gray for text */
            border-bottom: 1px solid #f3f4f6; /* Lighter border for rows */
        }
        .table-body tr:last-child td {
            border-bottom: none;
        }
        .table-body tr:hover {
            background-color: #f9fafb;
        }
        .table-footer td {
            padding: 16px;
            font-size: 1rem; /* text-base */
            font-weight: 600;
            background-color: #f3f4f6; /* Slightly darker footer */
            border-top: 1px solid #e5e7eb;
        }
    </style>
@endsection

@section('content')
    <div class="p-4 sm:p-6 md:p-8">
        <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Detail Pesanan #{{ $order->id }}</h1>
                <!-- Tombol Kembali ke Daftar Pesanan -->
                <a href="{{ route('mitra.orders') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition duration-300 ease-in-out shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Pesanan
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Informasi Pesanan -->
                <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Pesanan</h2>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">ID Pesanan:</strong> {{ $order->id }}</p>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">Tanggal Pesanan:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</p>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">Total Harga:</strong> <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">Status:</strong>
                        @php
                            $statusClass = '';
                            switch($order->status) {
                                case 'pending':
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'processing':
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'completed':
                                    $statusClass = 'bg-green-100 text-green-800';
                                    break;
                                case 'cancelled':
                                    $statusClass = 'bg-red-100 text-red-800';
                                    break;
                                case 'failed':
                                    $statusClass = 'bg-red-100 text-red-800';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800';
                            }
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    {{-- Tambahkan informasi pembayaran jika ada --}}
                    @if($order->payment)
                        <p class="mb-2 text-gray-700"><strong class="text-gray-900">Metode Pembayaran:</strong> {{ $order->payment->method ?? 'N/A' }}</p>
                        <p class="mb-2 text-gray-700"><strong class="text-gray-900">Status Pembayaran:</strong>
                            @php
                                $paymentStatusClass = '';
                                switch($order->payment->midtrans_transaction_status ?? '') {
                                    case 'settlement':
                                    case 'capture':
                                        $paymentStatusClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'pending':
                                        $paymentStatusClass = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'expire':
                                    case 'cancel':
                                    case 'deny':
                                        $paymentStatusClass = 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        $paymentStatusClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $paymentStatusClass }}">
                                {{ ucfirst($order->payment->midtrans_transaction_status ?? 'N/A') }}
                            </span>
                        </p>
                    @endif
                </div>

                <!-- Informasi Pelanggan -->
                <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Pelanggan</h2>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">Nama:</strong> {{ optional($order->user)->name ?? 'Tamu' }}</p>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">Email:</strong> {{ optional($order->user)->email ?? 'N/A' }}</p>
                    <p class="mb-2 text-gray-700"><strong class="text-gray-900">Telepon:</strong> {{ $order->shipping_phone ?? 'N/A' }}</p>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Alamat Pengiriman</h2>
                    <p class="mb-2 text-gray-700">{{ $order->shipping_address ?? 'N/A' }}</p>
                    <p class="mb-2 text-gray-700">{{ $order->shipping_city ?? 'N/A' }}, {{ $order->shipping_zip_code ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Daftar Item Pesanan -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Item Pesanan</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full bg-white">
                        <thead class="table-header">
                            <tr>
                                <th class="text-left">Produk</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            @forelse($order->items as $item)
                            <tr>
                                <td class="font-medium">{{ optional($item->product)->name ?? 'Produk Tidak Ditemukan' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500">Tidak ada item dalam pesanan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-footer">
                            <tr>
                                <td colspan="3" class="text-right">Total Pesanan:</td>
                                <td class="text-right font-bold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
