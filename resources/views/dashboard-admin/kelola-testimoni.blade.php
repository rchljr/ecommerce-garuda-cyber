@extends('layouts.db')

@section('title', 'Dashboard')
@section('content')
    <h1 class="main-title">Kelola Testimoni</h1>
    <div class="subtitle-row">
        <div class="main-subtitle">Daftar Testimoni Mitra</div>
        <div class="content-actions">
            <button class="search-btn" title="Cari">
                <svg width="20" height="20" fill="none">
                    <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            {{-- <button class="add-btn">
                <img src="images/tambah-db.png" alt="Tambah" class="add-btn-icon">
                <span>Tambah Kategori</span>
            </button> --}}
        </div>
    </div>
    <div class="table-container">
        <table class="mitra-table">
            <thead>
                <tr>
                    <th>NAMA MITRA</th>
                    <th>TESTIMONI</th>
                    <th>TANGGAL</th>
                    <th>RATING</th>
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
                    <td>Pelayanan sangat memuaskan dan produk berkualitas.</td>
                    <td>10 Juni 2025</td>
                    <td>
                        <span class="rating-stars">
                            &#9733;&#9733;&#9733;&#9733;&#9734; <!-- 4 bintang -->
                        </span>
                    </td>
                    <td><span class="badge aktif">Tampil</span></td>
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
                    <td>Pengiriman cepat dan customer service ramah.</td>
                    <td>8 Juni 2025</td>
                    <td>
                        <span class="rating-stars">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 bintang -->
                        </span>
                    </td>
                    <td><span class="badge segera">Menunggu</span></td>
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
                    <td>Kualitas produk kurang sesuai harapan.</td>
                    <td>5 Juni 2025</td>
                    <td>
                        <span class="rating-stars">
                            &#9733;&#9733;&#9734;&#9734;&#9734; <!-- 2 bintang -->
                        </span>
                    </td>
                    <td><span class="badge tidak">Tidak Tampil</span></td>
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