@extends('layouts.mitra')

@section('title', 'Edit Voucher')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">Edit Voucher: {{ $voucher->name }}</h4>
        </div>
        
        <form action="{{ route('mitra.vouchers.update', $voucher->id) }}" method="POST" class="p-6">
            @method('PUT')
            @include('mitra.vouchers._form')

            <div class="mt-6 flex justify-end">
                <a href="{{ route('mitra.vouchers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Batal
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Update Voucher
                </button>
            </div>
        </form>
    </div>
@endsection
