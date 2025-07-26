@extends('layouts.mitra')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Detail Transaksi Toko</h1>

        {{-- Ringkasan Global --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white shadow-lg rounded-lg p-6 text-center border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave mr-2 text-green-500"></i> Total Pendapatan
                </h3>
                <p class="text-4xl font-extrabold text-green-600 mt-3">{{ format_rupiah($totalAllRevenue) }}</p>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6 text-center border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd mr-2 text-red-500"></i> Total Modal
                </h3>
                <p class="text-4xl font-extrabold text-red-600 mt-3">{{ format_rupiah($totalAllCost) }}</p>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6 text-center border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center justify-center">
                    <i class="fas fa-chart-line mr-2 text-blue-500"></i> Total Keuntungan Bersih
                </h3>
                <p class="text-4xl font-extrabold text-blue-600 mt-3">{{ format_rupiah($totalAllProfit) }}</p>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Filter Transaksi</h2>
            <form action="{{ route('mitra.transactions.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end" id="filter-form">
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700">Rentang Tanggal:</label>
                    <select id="date_range" name="date_range"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                        <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir
                        </option>
                        <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir
                        </option>
                        <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
                    </select>
                </div>
                <div id="custom-date-inputs" class="{{ $dateRange !== 'custom' ? 'hidden' : '' }}">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date"
                        value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>
                <div id="custom-date-inputs-end" class="{{ $dateRange !== 'custom' ? 'hidden' : '' }}">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                </div>
                <div class="{{ $dateRange === 'custom' ? 'hidden' : '' }}"></div> {{-- Placeholder if custom inputs are
                shown --}}
                <div class="{{ $dateRange === 'custom' ? 'hidden' : '' }}"></div> {{-- Placeholder if custom inputs are
                shown --}}

                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama Pelanggan:</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ request('customer_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                        placeholder="Cari nama pelanggan">
                </div>
                <div>
                    <label for="delivery_method" class="block text-sm font-medium text-gray-700">Metode Pengiriman:</label>
                    <select id="delivery_method" name="delivery_method"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <option value="">Semua</option>
                        <option value="delivery" {{ request('delivery_method') == 'delivery' ? 'selected' : '' }}>Diantar
                        </option>
                        <option value="pickup" {{ request('delivery_method') == 'pickup' ? 'selected' : '' }}>Dijemput di Toko
                        </option>
                    </select>
                </div>
                <div>
                    <label for="delivery_service" class="block text-sm font-medium text-gray-700">Layanan
                        Pengiriman:</label>
                    <select id="delivery_service" name="delivery_service"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        <option value="">Semua</option>
                        @foreach($deliveryServices as $service)
                            <option value="{{ $service }}" {{ request('delivery_service') == $service ? 'selected' : '' }}>
                                {{ $service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1 md:col-span-2 lg:col-span-4 flex gap-4">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('mitra.transactions.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-sync-alt mr-2"></i> Reset Filter
                    </a>

                    {{-- BARU: Tombol Export untuk Laporan Produk --}}
                    <a href="{{ route('mitra.transactions.export.excel', request()->query()) }}"
                        class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md shadow-sm text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-file-excel mr-2"></i> Export Laporan Produk
                    </a>

                    {{-- OPSI: Jika Anda ingin tombol export langganan di halaman ini juga --}}
                    {{-- <a href="{{ route('mitra.pendapatan.export.excel', request()->query()) }}"
                        class="inline-flex items-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-md shadow-sm text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fas fa-file-excel mr-2"></i> Export Laporan Langganan
                    </a> --}}
                </div>
            </form>
        </div>

        {{-- Daftar Transaksi --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Pesanan Selesai (Completed)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Order</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pelanggan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Pendapatan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Modal</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keuntungan Bersih</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Pembayaran</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Metode Pengiriman</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Info Resi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($completedOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->order_date->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                                        {{ format_rupiah($order->total_price) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        {{ format_rupiah($order->calculated_cost) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">
                                        {{ format_rupiah($order->calculated_profit) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($order->status ?? 'N/A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst(str_replace('_', ' ', $order->delivery_method ?? 'Tidak Diketahui')) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($order->delivery_method == 'ship' && $order->shipping && $order->shipping->receipt_number)
                                            <p>{{ $order->shipping->delivery_service ?? 'N/A' }}</p>
                                            <p><strong>{{ $order->shipping->receipt_number }}</strong></p>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak
                                        ada transaksi yang selesai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $completedOrders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection