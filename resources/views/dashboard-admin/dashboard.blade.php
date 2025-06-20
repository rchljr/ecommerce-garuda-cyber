@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
    <div class="flex flex-col lg:flex-row gap-8 items-start">
        <!-- Kolom Kartu Statistik -->
        <div class="flex flex-col gap-8 w-full lg:w-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg w-full h-40 flex flex-col justify-between hover:shadow-xl transition-shadow">
                    <div class="text-base font-bold text-gray-600">Total Pendapatan Belum Lunas</div>
                    <div class="text-3xl font-bold text-[#FFC738]">{{ format_rupiah($totalPendapatanBelumLunas) }}</div>
                    <div class="text-sm text-gray-500">Dari seluruh transaksi</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg w-full h-40 flex flex-col justify-between hover:shadow-xl transition-shadow">
                    <div class="text-base font-bold text-gray-600">Mitra Perlu Verifikasi</div>
                    <div class="text-3xl font-bold text-[#4D80ED]">{{ number_format($jumlahMitraPerluVerifikasi) }}</div>
                    <div class="text-sm text-gray-500">Total pendaftar menunggu persetujuan</div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg w-full h-40 flex flex-col justify-between hover:shadow-xl transition-shadow">
                    <div class="text-base font-bold text-gray-600">Pendapatan Bulan Ini</div>
                    <div class="text-3xl font-bold text-green-500">{{ format_rupiah($totalPendapatanPerBulan) }}</div>
                    {{-- PERUBAHAN: Menggunakan helper format_tanggal --}}
                    <div class="text-sm text-gray-500">Total lunas di bulan {{ format_tanggal(now(), 'MMMM') }}</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg w-full h-40 flex flex-col justify-between hover:shadow-xl transition-shadow">
                    <div class="text-base font-bold text-gray-600">Jumlah Seluruh Akun Mitra</div>
                    <div class="text-3xl font-bold text-[#4D80ED]">{{ number_format($jumlahSeluruhAkunMitra) }}</div>
                    <div class="text-sm text-gray-500">Total mitra aktif saat ini</div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg w-full h-40 flex flex-col justify-between hover:shadow-xl transition-shadow">
                    <div class="text-base font-bold text-gray-600">Pendapatan Tahun Ini</div>
                    <div class="text-3xl font-bold text-green-500">{{ format_rupiah($totalPendapatanPerTahun) }}</div>
                    <div class="text-sm text-gray-500">Total lunas di tahun {{ date('Y') }}</div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-lg w-full h-40 flex flex-col justify-between hover:shadow-xl transition-shadow">
                    <div class="text-base font-bold text-gray-600">Jumlah Paket Terjual</div>
                    <div class="text-3xl font-bold text-[#4D80ED]">{{ number_format($jumlahPaketTerjual) }}</div>
                    <div class="text-sm text-gray-500">Total transaksi lunas di tahun {{ date('Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Kolom Chart -->
        <div class="flex-1 bg-white p-6 rounded-2xl shadow-lg flex flex-col min-w-[300px] lg:min-w-[500px] h-96 w-full lg:w-auto">
            <h3 class="font-semibold text-gray-700 mb-4">Total Paket Terjual Tiap Bulan (Tahun {{ date('Y') }})</h3>
            <div class="relative flex-grow">
                <canvas id="chart"></canvas>
            </div>
        </div>
</div>
@endsection

@push('scripts')
{{-- Pastikan Chart.js sudah di-load di layout utama Anda --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('chart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Paket Terjual',
                    // Data diambil dari variabel PHP dan di-encode ke JSON
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(220, 38, 38, 0.6)',
                    borderColor: 'rgba(185, 28, 28, 1)',
                    borderWidth: 1,
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // Pastikan ticks hanya menampilkan integer
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
