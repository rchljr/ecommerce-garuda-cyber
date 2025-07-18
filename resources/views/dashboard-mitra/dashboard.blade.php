@extends('layouts.mitra')
@section('title', 'Dashboard')

{{-- Menambahkan beberapa style kustom untuk Chart.js agar terlihat lebih baik --}}
@push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 350px;
            /* Atur tinggi default canvas */
            width: 100%;
        }
    </style>
@endpush


@section('content')
    <div class="bg-gray-100 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="container mx-auto">
            <!-- Header Dashboard -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Penjualan</h1>
                    <p class="text-gray-500 mt-1">Selamat datang kembali! Berikut adalah ringkasan bisnis Anda.</p>
                </div>

                {{-- Grup Tombol Aksi di Header --}}
                <div class="mt-4 sm:mt-0 flex items-center gap-3">
                    @php
                        // Mengambil data subdomain dari user yang sedang login
                        $subdomain = Auth::user()->shop->subdomain ?? null;
                        // Mengganti 'ecommercegaruda.my.id' dengan domain utama Anda jika berbeda
                        $storeUrl = $subdomain ? $subdomain->url : '#';
                    @endphp

                    @if($subdomain)
                        {{-- Tombol Kunjungi Toko --}}
                        <a href="{{ $storeUrl }}" target="_blank"
                            class="inline-flex items-center gap-2 bg-red-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Kunjungi Toko
                        </a>

                        {{-- ====================================================== --}}
                        {{-- == BAGIAN BARU: Tombol Publish / Unpublish == --}}
                        {{-- ====================================================== --}}
                        @if ($subdomain->status == 'active')
                            <form action="{{ route('mitra.editor.unpublish') }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menyembunyikan toko Anda dari publik?');">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-yellow-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors shadow-sm">
                                    <i class="fas fa-eye-slash"></i>
                                    Unpublish
                                </button>
                            </form>
                        @else
                            <form action="{{ route('mitra.editor.publish') }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin mempublikasikan toko Anda?');">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors shadow-sm">
                                    <i class="fas fa-globe-asia"></i>
                                    Publish
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Menampilkan notifikasi sukses atau error
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Sukses</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif --}}


            <!-- Grid untuk Kartu Metrik Utama (KPI) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Kartu Total Pendapatan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6">
                    <div class="bg-blue-100 p-3 rounded-full">
                        {{-- Ikon Dolar dari Heroicons --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a6 6 0 100-12 6 6 0 000 12z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalRevenue) }}</p>
                    </div>
                </div>

                <!-- Kartu Total Transaksi -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6">
                    <div class="bg-green-100 p-3 rounded-full">
                        {{-- Ikon Keranjang Belanja dari Heroicons --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
                    </div>
                </div>

                <!-- Kartu Nilai Pesanan Rata-rata -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        {{-- Ikon Grafik dari Heroicons --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nilai Pesanan Rata-rata</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($averageOrderValue) }}</p>
                    </div>
                </div>
            </div>

            <!-- Grid untuk Grafik dan Produk Terlaris -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kartu Grafik Pendapatan -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Pendapatan 30 Hari Terakhir</h3>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Kartu Produk Paling Laris -->
                <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">10 Produk Paling Laris</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">#</th>
                                    <th scope="col" class="px-4 py-3">Nama Produk</th>
                                    <th scope="col" class="px-4 py-3 text-center">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSellingProducts as $index => $product)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                            {{ Str::limit($product->name, 25) }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold">{{ $product->sold_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-10 text-gray-500">Belum ada produk yang terjual.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- CDN Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data dari Controller Laravel
            const salesData = {!! json_encode($salesData) !!};

            // Memformat data untuk Chart.js
            const labels = salesData.map(item => {
                // Mengubah format tanggal dari YYYY-MM-DD menjadi DD MMM
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });
            const data = salesData.map(item => item.revenue);

            // Konfigurasi Chart.js yang lebih menarik
            const chartConfig = {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (IDR)',
                        data: data,
                        borderColor: 'rgba(59, 130, 246, 0.8)', // Warna biru
                        backgroundColor: 'rgba(59, 130, 246, 0.1)', // Warna area di bawah garis
                        fill: true, // Mengaktifkan warna area
                        tension: 0.4, // Membuat garis melengkung (tidak kaku)
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(59, 130, 246, 1)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Penting agar chart mengisi container
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // Format angka di sumbu Y menjadi format Rupiah
                                callback: function (value, index, values) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false // Menghilangkan grid vertikal
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Menyembunyikan legenda karena sudah jelas dari judul
                        },
                        tooltip: {
                            // Kustomisasi tooltip saat di-hover
                            backgroundColor: '#1F2937',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            };

            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, chartConfig);
        });
    </script>
@endpush