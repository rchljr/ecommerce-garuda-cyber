@extends('layouts.db')

@section('title', 'Verifikasi Mitra')
@section('content')
<h1 class="main-title">Verifikasi Mitra</h1>
<div class="subtitle-row">
    <div class="main-subtitle">Daftar Pengajuan Mitra</div>
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
                <th>NAMA TOKO</th>
                <th>PEMILIK</th>
                <th>KATEGORI</th>
                <th>ALAMAT</th>
                <th>TAHUN BERDIRI</th>
                <th>DOKUMEN</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-weight: 700; color: black;">Toko ABC</div>
                    <div class="mitra-link">tokoabc.gci.com</div>
                </td>
                <td>
                    <div>Budi Santoso</div>
                    <div class="mitra-link">budi@email.com</div>
                    <div class="mitra-link">08123456789</div>
                </td>
                <td>Fashion</td>
                <td>Jl. Melati No. 123, Jakarta</td>
                <td>2018</td>
                <td>
                    <div class="doc-list">
                        <a href="uploads/foto-usaha.jpg" target="_blank" class="doc-link">Foto Usaha</a>
                        <a href="uploads/ktp.jpg" target="_blank" class="doc-link">KTP</a>
                        <a href="uploads/sku.pdf" target="_blank" class="doc-link">SKU</a>
                        <a href="uploads/npwp.pdf" target="_blank" class="doc-link">NPWP</a>
                        <a href="uploads/nib.pdf" target="_blank" class="doc-link">NIB</a>
                        <a href="uploads/iumk.pdf" target="_blank" class="doc-link">IUMK</a>
                    </div>
                </td>
                <td><span class="badge segera">Menunggu Verifikasi</span></td>
                <td>
                    <button class="action-btn" title="Approve">
                        <img src="images/approve.png" alt="Approve" class="icon-btn">
                    </button>
                    <button class="action-btn" title="Reject">
                        <img src="images/reject.png" alt="Reject" class="icon-btn">
                    </button>
                </td>
            </tr>
            <!-- Tambahkan baris lain sesuai kebutuhan -->
        </tbody>
    </table>
</div>
@endsection