@extends('layouts.db')

@section('title', 'Kelola Pendapatan')
@section('content')
<h1 class="main-title">Kelola Pendapatan</h1>
<div class="subtitle-row">
    <div class="main-subtitle">Daftar Pendapatan</div>
    <div class="content-actions">
        <button class="search-btn" title="Cari">
            <svg width="20" height="20" fill="none">
                <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <button class="export-btn"><img src="images/export.svg" alt="Export" class="export-btn-icon">Export
            Excel</button>
    </div>
</div>
<div class="table-container">
    <table class="income-table">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>NAMA MITRA</th>
                <th>PAKET</th>
                <th>JENIS TAGIHAN</th>
                <th>JUMLAH</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>01-06-2025</td>
                <td>Toko ABC</td>
                <td>Starter Plan</td>
                <td>Bulanan</td>
                <td>Rp150.000</td>
                <td><span class="badge paid">Lunas</span></td>
            </tr>
            <tr>
                <td>02-06-2025</td>
                <td>Toko DEF</td>
                <td>Business Plan</td>
                <td>Tahunan</td>
                <td>Rp3.000.000</td>
                <td><span class="badge unpaid">Belum Lunas</span></td>
            </tr>
            <tr>
                <td>03-06-2025</td>
                <td>Toko XYZ</td>
                <td>Enterprise Plan</td>
                <td>Tahunan</td>
                <td>Rp6.000.000</td>
                <td><span class="badge paid">Lunas</span></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection