@extends('layouts.customer')
@section('title', 'Pesanan Saya')

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
                                        <p class="text-sm text-gray-500">Toko: <span class="font-semibold text-gray-800">{{ optional($order->subdomain->user->shop)->shop_name ?? 'Toko Dihapus' }}</span></p>
                                        <p class="text-xs text-gray-400 mt-1">{{ format_tanggal($order->order_date) }}</p>
                                    </div>
                                    <div>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($order->status == 'completed') bg-green-100 text-green-800 @elseif($order->status == 'canceled') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                {{-- Body Order (Item) --}}
                                <div class="p-4 space-y-3">
                                    @foreach($order->items as $item)
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-start gap-4">
                                            {{-- Ganti dengan gambar produk jika ada --}}
                                            <div class="w-16 h-16 bg-gray-200 rounded-md"></div> 
                                            <div>
                                                <p class="font-semibold">{{ optional($item->product)->name ?? 'Produk Dihapus' }}</p>
                                                <p class="text-sm text-gray-500">{{ $item->quantity }} x {{ format_rupiah($item->price) }}</p>
                                            </div>
                                        </div>
                                        <p class="font-semibold">{{ format_rupiah($item->quantity * $item->price) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                {{-- Footer Order --}}
                                <div class="bg-gray-50 p-4 flex justify-between items-center rounded-b-lg">
                                    <p class="font-semibold">Total Pesanan: <span class="text-red-600">{{ format_rupiah($order->total_price) }}</span></p>
                                    <div>
                                        @if($order->status == 'completed')
                                            <button class="text-red-600 font-semibold border border-red-600 rounded-lg px-4 py-1 text-sm hover:bg-red-600 hover:text-white transition">Beri Ulasan</button>
                                        @else
                                            <button class="text-white font-semibold bg-red-600 rounded-lg px-4 py-1 text-sm hover:bg-red-700 transition">Lihat Detail</button>
                                        @endif
                                    </div>
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
@endsection