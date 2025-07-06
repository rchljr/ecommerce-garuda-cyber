@extends('layouts.mitra')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Detail Pesanan</h1>
            <p class="mt-1 text-sm text-gray-500">ID: <span class="font-mono">{{ $order->id }}</span></p>
        </div>
        <a href="{{ route('orders.index') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Item yang Dipesan</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <div class="p-6 flex items-start space-x-4">
                        <div class="flex-grow">
                            <p class="font-semibold text-gray-900">{{ $item->product->name ?? 'Produk tidak ditemukan' }}</p>
                            <p class="text-sm text-gray-500">Harga Satuan: Rp{{ number_format($item->unit_price) }}</p>
                        </div>
                        <div class="text-sm text-gray-600">x {{ $item->quantity }}</div>
                        <div class="text-right font-semibold text-gray-900">
                            Rp{{ number_format($item->unit_price * $item->quantity) }}
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="p-6 bg-gray-50 rounded-b-lg">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <p>Subtotal</p>
                        <p>Rp{{ number_format($order->total_price) }}</p>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-600 mt-2">
                        <p>Ongkos Kirim (Contoh)</p>
                        <p>Rp0</p>
                    </div>
                    <div class="flex justify-between items-center font-semibold text-gray-900 mt-4 pt-4 border-t border-gray-200">
                        <p>Total Harga</p>
                        <p>Rp{{ number_format($order->total_price) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-4">Informasi Pesanan</h2>
                <div class="mt-4 space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        @php
                            $statusClasses = 'bg-gray-100 text-gray-800';
                            switch (strtolower($order->status)) {
                                case 'pending': $statusClasses = 'bg-yellow-100 text-yellow-800'; break;
                                case 'paid': $statusClasses = 'bg-green-100 text-green-800'; break;
                                case 'shipped': $statusClasses = 'bg-blue-100 text-blue-800'; break;
                                case 'completed': $statusClasses = 'bg-indigo-100 text-indigo-800'; break;
                                case 'canceled': $statusClasses = 'bg-red-100 text-red-800'; break;
                            }
                        @endphp
                        <span class="px-3 py-1 font-semibold leading-tight rounded-full {{ $statusClasses }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tanggal Pesan</span>
                        <span class="font-medium text-gray-900">{{ $order->order_date->format('d M Y, H:i') }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-800">Pelanggan</h3>
                        <p class="mt-2 font-medium text-gray-900">{{ $order->user->name }}</p>
                        <p class="text-gray-500">{{ $order->user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection