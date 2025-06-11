@extends('layouts.db')

@section('title', 'Kelola Paket Berlangganan')
@section('content')
<h1 class="main-title">Kelola Paket Berlangganan</h1>
<div class="subtitle-row">
    <div class="main-subtitle">Daftar-Daftar Paket Berlangganan</div>
    <div class="content-actions">
        <button class="search-btn" title="Cari">
            <svg width="20" height="20" fill="none">
                <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <button class="add-btn">
            <img src="images/tambah-db.png" alt="Tambah" class="add-btn-icon">
            <span>Tambah Paket</span>
        </button>
    </div>
</div>
<div class="table-container">
    <table class="package-table">
        <thead>
            <tr>
                <th>NAMA PAKET</th>
                <th>FITUR</th>
                <th>DESKRIPSI SINGKAT</th>
                <th>HARGA</th>
                <th>DISKON TAHUNAN</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="package-name"><b>Starter Plan</b></td>
                <td>
                    <ul class="feature-list">
                        <li>Toko online lengkap dengan 2 tema gratis</li>
                        <li>Metode pembayaran fleksibel</li>
                        <li>Manajemen penyimpanan</li>
                        <li>Gratis subdomain (e.g., yourstore.garuda.id)</li>
                    </ul>
                </td>
                <td>
                    Untuk kebutuhan bisnis sederhana Anda
                </td>
                <td>
                    Rp150.000 / bulan<br>
                    Rp1.500.000 / tahun
                </td>
                <td>
                    10%
                </td>
                <td>
                    <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                            class="icon-btn"></button>
                    <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                            class="icon-btn"></button>
                </td>
            </tr>
            <tr>
                <td class="package-name"><b>Business Plan</b></td>
                <td>
                    <ul class="feature-list">
                        <li>Toko online lengkap dengan 5 tema</li>
                        <li>Metode pembayaran fleksibel</li>
                        <li>Manajemen penyimpanan</li>
                        <li>Gratis subdomain (e.g., yourstore.garuda.id)</li>
                    </ul>
                </td>
                <td>
                    Untuk bisnis menengah dan besar dengan kebutuhan lanjutan
                </td>
                <td>
                    Rp300.000 / bulan<br>
                    Rp3.000.000 / tahun
                </td>
                <td>
                    0%
                </td>
                <td>
                    <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                            class="icon-btn"></button>
                    <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                            class="icon-btn"></button>
                </td>
            </tr>
            <tr>
                <td class="package-name"><b>Enterprise Plan</b></td>
                <td>
                    <ul class="feature-list">
                        <li>Toko online lengkap dengan unlimited tema</li>
                        <li>Metode pembayaran fleksibel</li>
                        <li>Manajemen penyimpanan</li>
                        <li>Gratis subdomain (e.g., yourstore.garuda.id)</li>
                    </ul>
                </td>
                <td>
                    Solusi custom untuk perusahaan besar dengan kebutuhan e-commerce advanced
                </td>
                <td>
                    Rp600.000 / bulan<br>
                    Rp6.000.000 / tahun
                </td>
                <td>
                    0%
                </td>
                <td>
                    <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                            class="icon-btn"></button>
                    <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                            class="icon-btn"></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection