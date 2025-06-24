@extends('layouts.mitra')
@section('title', 'Daftar Produk')

@section('content')
<div class="container">
    <h2>Detail Produk</h2>

    <p><strong>Nama:</strong> {{ $product->name }}</p>
    <p><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>
    <p><strong>Harga:</strong> Rp{{ number_format($product->price) }}</p>
    <p><strong>Stok:</strong> {{ $product->stock }}</p>
    <p><strong>Deskripsi:</strong> {{ $product->description }}</p>
    <p><strong>Status:</strong> {{ $product->status }}</p>
    <p><strong>Thumbnail:</strong><br>
        @if ($product->thumbnail)
            <img src="{{ asset('storage/' . $product->thumbnail) }}" width="150">
        @endif
    </p>

    <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
