@extends('layouts.mitra')

@section('title', 'Edit Voucher')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="w-full mx-auto">
            <form action="{{ route('mitra.vouchers.update', $voucher->id) }}" method="POST">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-800">Edit Voucher</h1>
                        <p class="text-sm text-gray-500 mt-1">Perbarui detail voucher Anda.</p>
                    </div>

                    {{-- Memanggil file _form.blade.php yang sudah bersih --}}
                    @include('dashboard-mitra.vouchers._form')

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center gap-4">
                        <a href="{{ route('mitra.vouchers.index') }}"
                            class="text-gray-600 hover:text-gray-800 font-medium">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg transition-colors duration-300">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection