@extends('layouts.mitra')

@section('title', isset($voucher) ? 'Edit Voucher' : 'Buat Voucher Baru')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="w-full mx-auto">
            {{-- [PERBAIKAN] Menggunakan path URL relatif, bukan helper route() --}}
            <form action="{{ isset($voucher) ? '/mitra/vouchers/' . $voucher->id : '/mitra/vouchers' }}" method="POST">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-800">{{ isset($voucher) ? 'Edit Voucher' : 'Buat Voucher Baru' }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Isi detail di bawah ini untuk membuat atau memperbarui voucher Anda.</p>
                    </div>

                    <div class="p-6">
                        {{-- Menggunakan kode asli Anda di dalam div ini --}}
                        @csrf
                        {{-- Jika form ini juga dipakai untuk edit, pastikan ada input hidden untuk ID voucher --}}
                        @if (isset($voucher) && $voucher->id)
                            @method('PUT') {{-- Untuk metode PUT di update --}}
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="voucher_code" class="block text-sm font-medium text-gray-700">Nama Voucher / Kode Voucher</label>
                                <input type="text" name="voucher_code" id="voucher_code" value="{{ old('voucher_code', optional($voucher)->voucher_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cth: DISKONAKHIRTAHUN25" required>
                                @error('voucher_code') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="discount" class="block text-sm font-medium text-gray-700">Nilai Diskon (%)</label>
                                <input type="number" name="discount" id="discount" value="{{ old('discount', optional($voucher)->discount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="10" required min="1" max="100">
                                @error('discount') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Voucher</label>
                                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cth: Diskon 25% untuk pembelian di atas Rp 100.000">{{ old('description', optional($voucher)->description) }}</textarea>
                                @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="min_spending" class="block text-sm font-medium text-gray-700">Minimum Pembelian (Rp)</label>
                                <input type="number" name="min_spending" id="min_spending" value="{{ old('min_spending', optional($voucher)->min_spending ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="0">
                                @error('min_spending') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Berlaku Dari</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', optional($voucher)->start_date ? \Carbon\Carbon::parse(optional($voucher)->start_date)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                @error('start_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="expired_date" class="block text-sm font-medium text-gray-700">Berlaku Sampai</label>
                                <input type="date" name="expired_date" id="expired_date" value="{{ old('expired_date', optional($voucher)->expired_date ? \Carbon\Carbon::parse(optional($voucher)->expired_date)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                @error('expired_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 border-t pt-6 space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_for_new_customer" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200" @checked(old('is_for_new_customer', optional($voucher)->is_for_new_customer))>
                                <span class="ml-3 text-sm text-gray-600">Jadikan Voucher Selamat Datang untuk Customer Baru</span>
                            </label>

                            <label class="flex items-center">
                                {{-- [PERBAIKAN] Menggunakan variabel yang benar untuk checkbox ini --}}
                                <input type="checkbox" name="max_uses_per_customer" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200" @checked(old('max_uses_per_customer', (optional($voucher)->max_uses_per_customer ?? 0) == 1))>
                                <span class="ml-3 text-sm text-gray-600">Batasi Voucher Dapat Digunakan Hanya 1 Kali / Pelanggan</span>
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center gap-4">
                        {{-- [PERBAIKAN] Menggunakan path URL relatif, bukan helper route() --}}
                        <a href="/mitra/vouchers" class="text-gray-600 hover:text-gray-800 font-medium">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg transition-colors duration-300">
                            {{ isset($voucher) ? 'Simpan Perubahan' : 'Buat Voucher' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
