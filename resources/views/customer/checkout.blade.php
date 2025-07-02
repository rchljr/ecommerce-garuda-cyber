@extends('layouts.customer')
@section('title', 'Checkout')

@section('content')
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Kolom Kiri: Alamat & Pengiriman -->
                    <div class="w-full lg:w-2/3 space-y-8">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold mb-4">Alamat Pengiriman</h2>
                            {{-- Form Alamat di sini --}}
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold mb-4">Pilih Pengiriman</h2>
                            {{-- Integrasi RajaOngkir di sini --}}
                        </div>
                    </div>

                    <!-- Kolom Kanan: Ringkasan & Pembayaran -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24 space-y-4">
                            <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>
                            {{-- Daftar produk yang di-checkout --}}
                            <div class="flex justify-between"><span>Subtotal</span><span>Rp 100.000</span></div>
                            <div class="flex justify-between"><span>Ongkos Kirim</span><span>Rp 15.000</span></div>
                            <div class="flex justify-between font-bold text-lg border-t pt-4"><span>Total</span><span>Rp
                                    115.000</span></div>

                            <div class="pt-4">
                                <label for="voucher_code" class="font-semibold">Kode Voucher</label>
                                <div class="flex gap-2 mt-1">
                                    <input type="text" name="voucher_code" class="w-full border-gray-300 rounded-md">
                                    <button type="button" class="bg-gray-200 px-4 rounded-md">Terapkan</button>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full mt-6 bg-gray-800 text-white font-bold py-3 rounded-lg hover:bg-black">
                                Lanjut ke Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection