{{-- resources/views/mitra/dashboard/settings.blade.php --}}
@extends('layouts.mitra')

@section('title', 'Status Publikasi Toko')

@section('content')
    {{-- Notifikasi dipindahkan ke dalam section content --}}
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 1. Konten dibungkus dalam card agar lebih rapi --}}
    <div class="card text-center">
        <div class="card-header">
            Status Publikasi Toko
        </div>
        <div class="card-body p-4">
            @if (auth()->user()->subdomain?->publication_status === 'published')
                {{-- Tampilan untuk Toko yang Sudah Aktif --}}
                <h5 class="card-title">
                    <i class="fas fa-store text-success"></i> Toko Anda Aktif
                </h5>
                <p class="card-text text-muted">Toko Anda saat ini dapat diakses oleh publik.</p>
                <form action="{{ route('mitra.store.unpublish') }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menyembunyikan toko Anda dari publik?');">
                    @csrf
                    <button type="submit" class="btn btn-warning mt-3">
                        <i class="fas fa-eye-slash"></i> Sembunyikan Toko (Unpublish)
                    </button>
                </form>
            @else
                {{-- Tampilan untuk Toko yang Belum Aktif --}}
                <h5 class="card-title">
                    <i class="fas fa-pause-circle text-secondary"></i> Toko Belum Dipublikasikan
                </h5>
                <p class="card-text text-muted">Selesaikan pengaturan toko Anda, lalu publikasikan agar dapat dikunjungi
                    pelanggan.</p>
                {{-- GANTI KODE FORM LAMA ANDA DENGAN YANG INI --}}

                <form action="{{ route('mitra.store.publish') }}" method="POST" id="publishForm">
                    @csrf
                    {{-- Kita sembunyikan tombol submit asli di dalam form --}}
                    <button type="submit" style="display: none;"></button>
                </form>

                {{-- Kita buat tombol baru di luar form yang akan memicu submit secara manual --}}
                <button type="button" onclick="document.getElementById('publishForm').submit();"
                    class="btn btn-primary mt-3">
                    <i class="fas fa-rocket"></i> Publikasikan Toko Sekarang
                </button>
            @endif
        </div>
        <div class="card-footer text-muted">
            Perubahan status akan langsung diterapkan.
        </div>
    </div>
@endsection
