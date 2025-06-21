@extends('layouts.mitra')
@section('title', 'Daftar Produk')

@section('content')
<div class="container">
    <h2>Edit Produk</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('products._form', ['product' => $product])
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
