@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tambah Produk</h1>

    <form action="{{ route('dashboard-mitra.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('dashboard-mitra.products._form', ['submit' => 'Simpan'])

    </form>
</div>
@endsection
