@extends('layouts.customer')
@section('title', 'Voucher Saya')

@section('content')
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar Kiri -->
            <aside class="w-full md:w-1/4 lg:w-1/5 flex-shrink-0">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    @include('layouts._partials.customer-sidebar')
                </div>
            </aside>

            <!-- Konten Utama Kanan -->
            <main class="w-full md:w-3/4 lg:w-4/5">
                <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold">Voucher Saya</h1>
                            <p class="text-gray-500 mt-1">Daftar Voucher Saya</p>
                        </div>
                        <form action="{{ route('customer.vouchers.claim') }}" method="POST"
                            class="flex items-center w-full sm:w-auto">
                            @csrf
                            <input type="text" name="voucher_code"
                                class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Punya kode promo? Masukkan di sini">
                            <button type="submit"
                                class="bg-red-600 text-white font-semibold px-4 py-2 rounded-r-lg hover:bg-red-700">Simpan</button>
                        </form>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @error('voucher_code')
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ $message }}</p>
                        </div>
                    @enderror

                    <!-- Daftar Voucher -->
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        @forelse ($vouchers as $voucher)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm flex items-center p-4 gap-4">
                                <div class="flex-grow">
                                    <h3 class="font-bold text-gray-800">Diskon {{ format_rupiah($voucher->discount) }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Minimal Transaksi
                                        {{ format_rupiah($voucher->min_spending) }}</p>
                                    <p class="text-xs text-gray-400 mt-2">Berlaku hingga:
                                        {{ format_tanggal($voucher->expired_date) }}</p>
                                </div>
                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <button class="text-gray-400 hover:text-gray-600" title="Syarat & Ketentuan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <button
                                        class="bg-red-600 text-white text-sm font-semibold px-6 py-2 rounded-lg hover:bg-red-700">Pakai</button>
                                </div>
                            </div>
                        @empty
                            <div class="xl:col-span-2 text-center py-12 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                    </path>
                                </svg>
                                <p class="mt-4">Anda belum memiliki voucher.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginasi -->
                    <div class="mt-8">
                        {{ $vouchers->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection