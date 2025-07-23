@extends('layouts.mitra')
@section('title', 'Dashboard')

{{-- Menambahkan beberapa style kustom untuk Chart.js agar terlihat lebih baik --}}
@push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        /* Style untuk kartu yang bisa diklik */
        .clickable-card {
            cursor: pointer;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Kelas warna untuk kartu metrik */
        .card-revenue {
            border-left: 4px solid #3b82f6; /* Blue-500 */
        }
        .card-orders {
            border-left: 4px solid #22c55e; /* Green-500 */
        }
        .card-avg-order {
            border-left: 4px solid #f59e0b; /* Yellow-500 */
        }
        .card-profit {
            border-left: 4px solid #8b5cf6; /* Purple-500 */
        }

        /* Ikon Font Awesome untuk kartu */
        .card-icon {
            font-size: 2.5rem; /* Ukuran ikon */
            color: currentColor; /* Mengikuti warna teks kartu */
        }

        /* Gaya untuk mencegah overflow teks di kartu */
        .kpi-card .text-3xl, /* Angka metrik */
        .kpi-card .text-lg.font-semibold /* Judul kartu */ {
            white-space: nowrap; /* Mencegah teks/angka pindah baris */
            overflow: hidden; /* Sembunyikan jika melampaui batas */
            text-overflow: ellipsis; /* Tampilkan elipsis jika terpotong */
            max-width: 100%; /* Pastikan tidak melebihi lebar kontainer */
            display: block; /* Pastikan overflow/ellipsis bekerja */
        }
    </style>
@endpush


@section('content')
    <div class="bg-gray-100 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="container mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Penjualan</h1>
                    <p class="text-gray-500 mt-1">Selamat datang kembali! Berikut adalah ringkasan bisnis Anda.</p>
                </div>

                {{-- Grup Tombol Aksi di Header --}}
                <div class="mt-4 sm:mt-0 flex items-center gap-3">
                    @php
                        $subdomain = Auth::user()->shop->subdomain ?? null;
                        $storeUrl = $subdomain ? $subdomain->subdomain_name : '#';
                    @endphp

                    @if ($subdomain)
                        {{-- Tombol Kunjungi Toko --}}
                        <a href="{{'https://ecommercegaruda.my.id/tenant/'. $storeUrl . '/home'}}" target="_blank"
                            class="inline-flex items-center gap-2 bg-red-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6l-8 8-4-4-6 6" />
                            </svg>
                            Kunjungi Toko
                        </a>

                        {{-- ====================================================== --}}
                        {{-- == BAGIAN BARU: Tombol Publish / Unpublish == --}}
                        {{-- ====================================================== --}}
                        @if (Auth::user()->shop->publication_status == 'publish')
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

            {{-- Menampilkan notifikasi sukses atau error --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                    <p class="font-bold">Sukses</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif


            {{-- UBAH: Mengatur grid agar selalu menampilkan 2 kolom pada ukuran md ke atas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6 clickable-card card-revenue kpi-card">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-blue-500 card-icon"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalRevenue) }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6 clickable-card card-orders kpi-card">
                    <a href="{{ route('mitra.orders.index') }}" class="flex items-center gap-6 w-full h-full">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-shopping-cart text-green-500 card-icon"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
                            <p class="text-xs text-gray-400 mt-1">Klik untuk detail transaksi</p>
                        </div>
                    </a>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6 clickable-card card-avg-order kpi-card">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-chart-bar text-yellow-500 card-icon"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nilai Pesanan Rata-rata</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($averageOrderValue) }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-6 clickable-card card-profit kpi-card">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-wallet text-purple-500 card-icon"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Keuntungan Bersih</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalNetProfit) }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Pendapatan Berdasarkan Periode</h3>
                        {{-- Filter untuk Grafik --}}
                        <form id="chart-filter-form" method="GET" action="{{ route('mitra.dashboard') }}">
                            <select name="chart_range" id="chart-range-select"
                                class="mt-1 block w-auto rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                onchange="this.form.submit()">
                                <option value="7_days" {{ $chartRange == '7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                                <option value="30_days" {{ $chartRange == '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                                <option value="this_month" {{ $chartRange == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="this_year" {{ $chartRange == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                            </select>
                        </form>
                    </div>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
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
                                        <td colspan="3" class="text-center py-10 text-gray-500">Belum ada produk yang
                                            terjual.
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
        document.addEventListener('DOMContentLoaded', function() {
            // Data dari Controller Laravel
            const salesData = {!! json_encode($salesData) !!};

            // Memformat data untuk Chart.js
            const labels = salesData.map(item => {
                // Mengubah format tanggal dari YYYY-MM-DD menjadi DD MMM
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });
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
                                callback: function(value, index, values) {
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
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(context.parsed.y);
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