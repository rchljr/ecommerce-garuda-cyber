@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Daftar Produk</h1>

    <a href="{{ route('dashboard-mitra.products.create') }}" class="btn btn-primary mb-3">+ Tambah Produk</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Thumbnail</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ ucfirst($product->status) }}</td>
                <td>
                    @if($product->thumbnail)
                        <img src="{{ asset('storage/thumbnails/'.$product->thumbnail) }}" width="50" alt="Thumbnail">
                    @endif
                </td>
                <td>
                    <a href="{{ route('dashboard-mitra.products.show', $product->id) }}" class="btn btn-sm btn-info">Detail</a>
                    <a href="{{ route('dashboard-mitra.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard-mitra.products.destroy', $product->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

