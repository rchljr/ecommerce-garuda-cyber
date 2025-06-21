@extends('layouts.mitra')
@section('title', 'Daftar Produk')

@section('content')
<div class="container">
    <h2>Tambah Produk</h2>

    <form action="{{ route('mitra.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('dashboard-mitra.products._form', ['product' => null])
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
