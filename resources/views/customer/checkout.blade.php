@extends('layouts.customer')
@section('title', 'Checkout')

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp
    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

            {{-- Pastikan form mengarah ke rute yang benar dengan metode POST --}}
            <form action="{{ route('tenant.checkout.process', ['subdomain' => $currentSubdomain]) }}" method="POST">
                @csrf

                {{-- Simpan ID item yang di-checkout di hidden input untuk dikirim saat proses --}}
                @foreach ($checkoutItems as $item)
                    <input type="hidden" name="items[]" value="{{ $item->id ?? $item['id'] }}">
                @endforeach

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Kolom Kiri: Alamat & Pengiriman -->
                    <div class="w-full lg:w-2/3 space-y-8">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold mb-4">Alamat Pengiriman</h2>
                            {{-- Form Alamat di sini --}}
                            <p class="text-gray-500">Integrasi form alamat pengiriman...</p>
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold mb-4">Pilih Pengiriman</h2>
                            {{-- Integrasi RajaOngkir di sini --}}
                            <p class="text-gray-500">Integrasi pilihan kurir (RajaOngkir)...</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Ringkasan & Pembayaran -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24 space-y-4">
                            <h2 class="text-xl font-bold border-b pb-4">Ringkasan Pesanan</h2>

                            {{-- PERBAIKAN: Tampilkan daftar produk yang di-checkout secara dinamis --}}
                            <div class="space-y-4 max-h-64 overflow-y-auto pr-2">
                                @forelse ($checkoutItems as $item)
                                    @php
                                        $product = is_object($item->product) ? $item->product : (object) ($item['product'] ?? []);
                                        $quantity = $item->quantity ?? ($item['quantity'] ?? 0);
                                        $price = $product->price ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-4 text-sm">
                                        <img src="{{ asset('storage/' . ($product->main_image ?? 'images/placeholder.png')) }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/64x64/f1f5f9/cbd5e1?text=No+Image';"
                                            alt="{{ $product->name }}" class="w-16 h-16 rounded-md object-cover">
                                        <div class="flex-grow">
                                            <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                            <p class="text-gray-500">{{ $quantity }} x {{ format_rupiah($price) }}</p>
                                        </div>
                                        <p class="font-semibold text-gray-800">{{ format_rupiah($price * $quantity) }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500">Tidak ada item yang di-checkout.</p>
                                @endforelse
                            </div>

                            {{-- PERBAIKAN: Tampilkan total dari controller --}}
                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span id="subtotal-amount">{{ format_rupiah($subtotal) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Ongkos Kirim</span>
                                    <span id="shipping-cost">Rp 0</span> {{-- Akan diupdate oleh JS dari RajaOngkir --}}
                                </div>
                                <div class="flex justify-between font-bold text-lg border-t pt-4 mt-2">
                                    <span>Total</span>
                                    <span id="total-amount">{{ format_rupiah($subtotal) }}</span> {{-- Akan diupdate oleh JS
                                    --}}
                                </div>
                            </div>

                            <div class="pt-4">
                                <label for="voucher_code" class="font-semibold">Kode Voucher</label>
                                <div class="flex gap-2 mt-1">
                                    <input type="text" name="voucher_code" class="w-full border-gray-300 rounded-md"
                                        placeholder="Masukkan kode">
                                    <button type="button" class="bg-gray-200 px-4 rounded-md text-sm">Terapkan</button>
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