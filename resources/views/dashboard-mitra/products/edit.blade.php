@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Produk</h1>

    <form action="{{ route('dashboard-mitra.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('dashboard-mitra.products._form', ['submit' => 'Update'])

    </form>
</div>
@endsection
