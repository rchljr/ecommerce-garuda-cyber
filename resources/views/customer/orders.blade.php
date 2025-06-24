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
                            <p class="text-gray-500 mt-1">Daftar Pesanan Saya</p>
                        </div>
                        <div class="relative w-full max-w-sm">
                            <form action="{{ route('customer.orders') }}" method="GET">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    class="block w-full pl-10 pr-4 py-2 h-12 border border-gray-300 rounded-lg bg-white focus:border-blue-500 focus:ring-blue-500 transition"
                                    placeholder="Cari pesanan...">
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Pesanan -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-medium text-gray-600">Nama Produk</th>
                                    <th scope="col" class="px-6 py-3 font-medium text-gray-600">Detail Pesanan</th>
                                    <th scope="col" class="px-6 py-3 font-medium text-gray-600">Harga</th>
                                    <th scope="col" class="px-6 py-3 font-medium text-gray-600">Total Pesanan</th>
                                    <th scope="col" class="px-6 py-3 font-medium text-gray-600">Status</th>
                                    <th scope="col" class="px-6 py-3 font-medium text-gray-600 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                {{-- Data Dummy - Ganti dengan data asli dari $orders --}}
                                @for ($i = 0; $i < 5; $i++)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-semibold">Baju Dress Korea</div>
                                            <div class="text-xs text-gray-500">x1</div>
                                        </td>
                                        <td class="px-6 py-4">Ukuran M</td>
                                        <td class="px-6 py-4">{{ format_rupiah(100000) }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ format_rupiah(100000) }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                class="text-red-600 font-semibold border border-red-600 rounded-lg px-4 py-1 text-sm hover:bg-red-600 hover:text-white transition">Nilai</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-semibold">Celana Oro Pants</div>
                                            <div class="text-xs text-gray-500">x2</div>
                                        </td>
                                        <td class="px-6 py-4">Ukuran S</td>
                                        <td class="px-6 py-4">{{ format_rupiah(120000) }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ format_rupiah(240000) }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                class="text-white font-semibold bg-red-600 rounded-lg px-4 py-1 text-sm hover:bg-red-700 transition">Beli
                                                Lagi</button>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginasi -->
                    <div class="mt-6">
                        {{-- {{ $orders->links() }} --}}
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection