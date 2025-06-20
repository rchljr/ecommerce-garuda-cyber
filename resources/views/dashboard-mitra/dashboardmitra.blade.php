@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
    <h1 class="main-title">Dashboard</h1>
    <div class="dashboard-content">
        <div class="dashboard-main-row">
            <div class="dashboard-col">
                <div class="dashboard-card">
                    <div class="card-title">Total Pendapatan Per Bulan</div>
                    <div class="card-value orange">Rp 000,000</div>
                    <div class="card-desc">dari Total Bulan ini</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title">Total Pendapatan Per Tahun</div>
                    <div class="card-value orange">Rp 000,000</div>
                    <div class="card-desc">dari Total Tahun ini</div>
                </div>
            </div>
            <div class="dashboard-col">
                <div class="dashboard-card">
                    <div class="card-title">Jumlah Seluruh Akun Mitra</div>
                    <div class="card-value blue">000</div>
                    <div class="card-desc">dari Jumlah di Tahun ini</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-title">Jumlah Paket Berlangganan Terjual</div>
                    <div class="card-value blue">000</div>
                    <div class="card-desc">dari Jumlah di tahun ini</div>
                </div>
            </div>
            <div class="dashboard-col dashboard-chart-card">
                <div class="chart-title">Total Paket Terjual Tiap Bulan</div>
                <canvas id="chart" height="180"></canvas>
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
                    backgroundColor: '#60C12C'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endpush