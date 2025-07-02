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
                            <p class="text-gray-500 mt-1">Daftar voucher yang tersedia dari semua toko.</p>
                        </div>
                    </div>

                    <!-- Daftar Voucher -->
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        @forelse ($vouchers as $voucher)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col p-4">
                                <div class="flex-grow">
                                    <p class="text-sm text-gray-500">Dari: <span class="font-semibold">{{ data_get($voucher, 'subdomain.user.shop.shop_name', 'Informasi Toko Tidak Tersedia') }}</span></p>
                                    <h3 class="font-bold text-gray-800 text-lg my-2">Diskon
                                        {{ format_rupiah($voucher->discount) }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">Minimal Transaksi
                                        {{ format_rupiah($voucher->min_spending) }}</p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-dashed">
                                    <p class="text-xs text-gray-400">Berlaku hingga:
                                        {{ format_tanggal($voucher->expired_date) }}</p>
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
                                <p class="mt-4">Tidak ada voucher yang tersedia saat ini.</p>
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