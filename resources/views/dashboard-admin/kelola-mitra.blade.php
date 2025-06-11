@extends('layouts.db')

@section('title', 'Kelola Mitra')
@section('content')
<h1 class="main-title">Kelola Mitra</h1>
<div class="subtitle-row">
    <div class="main-subtitle">Daftar-Daftar Mitra</div>
    <div class="content-actions">
        <button class="search-btn" title="Cari">
            <svg width="20" height="20" fill="none">
                <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</div>
<div class="table-container">
    <table class="mitra-table">
        <thead>
            <tr>
                <th>NAMA</th>
                <th>PILIHAN PAKET</th>
                <th>MASA BERLAKU</th>
                <th>TANGGAL BERAKHIR</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-weight: 700; color: black;">Toko ABC</div>
                    <a href="#" class="mitra-link">tokoabc.gci.com</a>
                </td>
                <td>Starter Plan</td>
                <td>1 tahun</td>
                <td>31 Desember 2024</td>
                <td><span class="badge aktif">Aktif</span></td>
                <td>
                    <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                            class="icon-btn"></button>
                    <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                            class="icon-btn"></button>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight: 700; color: black;">Toko ABC</div>
                    <a href="#" class="mitra-link">tokoabc.gci.com</a>
                </td>
                <td>Starter Plan</td>
                <td>1 tahun</td>
                <td>31 Desember 2024</td>
                <td><span class="badge segera">Segera Berakhir</span></td>
                <td>
                    <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                            class="icon-btn"></button>
                    <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                            class="icon-btn"></button>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight: 700; color: black;">Toko ABC</div>
                    <a href="#" class="mitra-link">tokoabc.gci.com</a>
                </td>
                <td>Starter Plan</td>
                <td>1 tahun</td>
                <td>31 Desember 2024</td>
                <td><span class="badge tidak">Tidak Aktif</span></td>
                <td>
                    <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                            class="icon-btn"></button>
                    <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                            class="icon-btn"></button>
                </td>
            </tr>
            <!-- Tambahkan baris lain sesuai kebutuhan -->
        </tbody>
    </table>
</div>
@endsection