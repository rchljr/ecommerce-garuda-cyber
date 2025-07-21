@extends('layouts.mitra')

@section('title', 'Detail Penjualan Transaksi')

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
            /* Latar belakang abu-abu muda */
        }

        /* Scrollbar kustom untuk tabel jika diperlukan */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection

@section('content')
    <div class="p-4 sm:p-6 md:p-8"> {{-- Removed body tag, added padding to main div --}}
        <div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Detail Penjualan Transaksi</h1>

            <!-- Tombol Kembali ke Dashboard -->
            <div class="mb-6">
                <a href="/dashboard"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition duration-300 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- Bagian Daftar Transaksi -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Transaksi Terbaru</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th
                                    class="py-3 px-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                    ID Pesanan</th>
                                <th
                                    class="py-3 px-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="py-3 px-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                    Pelanggan</th>
                                <th
                                    class="py-3 px-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                    Total Harga</th>
                                <th
                                    class="py-3 px-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="py-3 px-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop melalui data transaksi di sini di Laravel Blade -->
                            <!-- Baris contoh (ganti dengan data dinamis) -->
                            <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">ORD-001</td>
                                <td class="py-3 px-4 text-sm text-gray-700">2025-07-19</td>
                                <td class="py-3 px-4 text-sm text-gray-700">Budi Santoso</td>
                                <td class="py-3 px-4 text-sm text-gray-700">Rp 150.000</td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <a href="/transactions/ORD-001"
                                        class="text-blue-600 hover:text-blue-800 font-medium">Lihat Detail</a>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">ORD-002</td>
                                <td class="py-3 px-4 text-sm text-gray-700">2025-07-18</td>
                                <td class="py-3 px-4 text-sm text-gray-700">Siti Aminah</td>
                                <td class="py-3 px-4 text-sm text-gray-700">Rp 85.000</td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <a href="/transactions/ORD-002"
                                        class="text-blue-600 hover:text-blue-800 font-medium">Lihat Detail</a>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">ORD-003</td>
                                <td class="py-3 px-4 text-sm text-gray-700">2025-07-17</td>
                                <td class="py-3 px-4 text-sm text-gray-700">Joko Susilo</td>
                                <td class="py-3 px-4 text-sm text-gray-700">Rp 220.000</td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <a href="/transactions/ORD-003"
                                        class="text-blue-600 hover:text-blue-800 font-medium">Lihat Detail</a>
                                </td>
                            </tr>
                            <!-- Tambahkan lebih banyak baris sesuai kebutuhan -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
