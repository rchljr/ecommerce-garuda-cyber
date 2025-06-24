@extends('layouts.mitra')
@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Dashboard Mitra</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Total Pendapatan Bulan Ini --}}
        <div class="bg-orange-100 border-l-4 border-orange-500 p-5 rounded-xl shadow hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-orange-700">Total Pendapatan Bulan Ini</h2>
            <p class="text-2xl font-bold text-orange-800 mt-2">Rp 000,000</p>
            <p class="text-sm text-gray-600 mt-1">Diperoleh dari semua transaksi bulan ini</p>
        </div>

        {{-- Total Pendapatan Tahun Ini --}}
        <div class="bg-orange-100 border-l-4 border-orange-500 p-5 rounded-xl shadow hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-orange-700">Total Pendapatan Tahun Ini</h2>
            <p class="text-2xl font-bold text-orange-800 mt-2">Rp 000,000</p>
            <p class="text-sm text-gray-600 mt-1">Akumulasi dari Januari hingga sekarang</p>
        </div>

        {{-- Jumlah Mitra --}}
        <div class="bg-blue-100 border-l-4 border-blue-500 p-5 rounded-xl shadow hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-blue-700">Jumlah Seluruh Akun Mitra</h2>
            <p class="text-2xl font-bold text-blue-800 mt-2">000</p>
            <p class="text-sm text-gray-600 mt-1">Yang terdaftar tahun ini</p>
        </div>

        {{-- Paket Terjual --}}
        <div class="bg-blue-100 border-l-4 border-blue-500 p-5 rounded-xl shadow hover:shadow-md transition">
            <h2 class="text-lg font-semibold text-blue-700">Paket Berlangganan Terjual</h2>
            <p class="text-2xl font-bold text-blue-800 mt-2">000</p>
            <p class="text-sm text-gray-600 mt-1">Selama tahun berjalan</p>
        </div>

        {{-- Grafik Penjualan --}}
        <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white border rounded-xl p-6 shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📈 Paket Terjual Tiap Bulan</h2>

            <div class="overflow-x-auto">
                <div style="min-width: 800px; height: 300px;">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets: [{
                    label: 'Paket Terjual',
                    data: [32, 42, 38, 45, 41, 36, 50, 55, 44, 47, 23, 40],
                    backgroundColor: '#60C12C',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
