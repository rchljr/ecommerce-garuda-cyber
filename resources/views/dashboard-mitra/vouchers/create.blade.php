@extends('layouts.mitra')

@section('title', 'Buat Voucher Baru')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">Formulir Voucher Baru</h4>
        </div>
        
        <form action="{{ route('mitra.vouchers.store') }}" method="POST" class="p-6">
            @include('dashboard-mitra.vouchers._form')

            <div class="mt-6 flex justify-end">
                <a href="{{ route('mitra.vouchers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Voucher
                </button>
            </div>
        </form>
    </div>
@endsection
