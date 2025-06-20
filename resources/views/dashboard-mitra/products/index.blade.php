@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
    <h1>Daftar Produk</h1>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('dashboard-mitra.products.create') }}" class="btn btn-create">+ Tambah Produk</a>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->status }}</td>
                    <td>
                        <a href="{{ route('dashboard-mitra.products.edit', $product->id) }}" class="btn btn-edit">Edit</a>
                        <form action="{{ route('dashboard-mitra.products.destroy', $product->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('Yakin hapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
