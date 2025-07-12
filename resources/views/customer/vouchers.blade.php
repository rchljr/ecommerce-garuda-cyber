@extends('layouts.customer')
@section('title', 'Voucher Saya')

@push('styles')
    <style>
        .sk-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
            padding: 0 1rem;
        }

        .sk-panel.show {
            max-height: 100px;
            /* Atur tinggi maksimal yang cukup */
            padding-top: 0.75rem;
            padding-bottom: 0.25rem;
        }
    </style>
@endpush

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
                            <h1 class="text-2xl md:text-3xl font-bold">Voucher Tersedia</h1>
                            <p class="text-gray-500 mt-1">Klaim dan gunakan voucher di toko favorit Anda.</p>
                        </div>
                    </div>

                    {{-- Navigasi Tab --}}
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <a href="{{ request()->url() }}"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ !request('tab') ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Toko Saat Ini
                            </a>
                            <a href="{{ request()->url() }}?tab=other"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('tab') === 'other' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Dari Toko Lain
                            </a>
                        </nav>
                    </div>

                    {{-- Konten Tab --}}
                    <div class="mt-6">
                        @if(request('tab') === 'other')
                            {{-- Tampilan untuk Voucher Toko Lain --}}
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                @forelse ($otherStoreVouchers as $voucher)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col">
                                        <div class="p-4">
                                            <p class="text-sm text-gray-500">Dari: <span
                                                    class="font-semibold">{{ optional($voucher->user->shop)->shop_name ?? 'Toko' }}</span>
                                            </p>
                                            <h3 class="font-bold text-gray-800 text-lg my-2">Diskon {{ $voucher->discount }}%</h3>
                                            <p class="text-sm text-gray-600 mt-1">Minimal Transaksi
                                                {{ format_rupiah($voucher->min_spending) }}</p>
                                            <p class="text-xs text-gray-400 mt-2">Berlaku hingga:
                                                {{ format_tanggal($voucher->expired_date) }}</p>
                                        </div>
                                        {{-- PERBAIKAN: Menambahkan atribut ID yang hilang --}}
                                        <div class="sk-panel bg-gray-50 text-xs text-gray-600" id="sk-panel-other-{{$voucher->id}}">
                                            {{ $voucher->description }}
                                        </div>
                                        <div
                                            class="mt-auto pt-3 px-4 pb-4 border-t border-dashed flex justify-between items-center">
                                            <button class="sk-toggle-button text-sm text-gray-500 hover:text-red-600"
                                                data-target="sk-panel-other-{{$voucher->id}}">S&K</button>
                                            @if(optional($voucher->subdomain)->subdomain_name)
                                                <a href="{{ route('tenant.home', ['subdomain' => $voucher->subdomain->subdomain_name]) }}"
                                                    class="bg-red-600 text-white font-semibold px-4 py-1.5 text-sm rounded-md hover:bg-red-700 transition">
                                                    Kunjungi Toko
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="xl:col-span-2 text-center py-12 text-gray-500">
                                        <p>Tidak ada voucher dari toko lain yang tersedia saat ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        @else
                            {{-- Tampilan untuk Voucher Toko Saat Ini --}}
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                @forelse ($currentStoreVouchers as $voucher)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col">
                                        <div class="p-4">
                                            <p class="text-sm text-gray-500">Dari: <span
                                                    class="font-semibold">{{ optional($voucher->user->shop)->shop_name ?? 'Toko Ini' }}</span>
                                            </p>
                                            <h3 class="font-bold text-gray-800 text-lg my-2">Diskon {{ $voucher->discount }}%</h3>
                                            <p class="text-sm text-gray-600 mt-1">Minimal Transaksi
                                                {{ format_rupiah($voucher->min_spending) }}</p>
                                            <p class="text-xs text-gray-400 mt-2">Berlaku hingga:
                                                {{ format_tanggal($voucher->expired_date) }}</p>
                                        </div>
                                        {{--  Menambahkan ID unik ke panel S&K --}}
                                        <div class="sk-panel bg-gray-50 text-xs text-gray-600" id="sk-panel-current-{{$voucher->id}}">
                                            {{ $voucher->description }}
                                        </div>
                                        <div
                                            class="mt-auto pt-3 px-4 pb-4 border-t border-dashed flex justify-between items-center">
                                            {{-- Menyesuaikan data-target agar unik --}}
                                            <button class="sk-toggle-button text-sm text-gray-500 hover:text-red-600"
                                                data-target="sk-panel-current-{{$voucher->id}}">S&K</button>
                                            <a href="{{ route('tenant.shop', ['subdomain' => request()->route('subdomain')]) }}"
                                                class="bg-red-600 text-white font-semibold px-4 py-1.5 text-sm rounded-md hover:bg-red-700 transition">
                                                Gunakan
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="xl:col-span-2 text-center py-12 text-gray-500">
                                        <p>Toko ini belum memiliki voucher yang tersedia.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- LOGIKA ACCORDION UNTUK S&K ---
            const allSkToggleButtons = document.querySelectorAll('.sk-toggle-button');

            allSkToggleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Dapatkan panel target dari atribut data-target
                    // Menggunakan getElementById lebih efisien untuk ID
                    const targetPanel = document.getElementById(this.dataset.target);

                    if (!targetPanel) {
                        return; // Jika panel tidak ditemukan, hentikan eksekusi
                    }

                    // Cek apakah panel yang diklik saat ini sudah terbuka
                    const isCurrentlyOpen = targetPanel.classList.contains('show');

                    // 1. Tutup semua panel yang sedang terbuka
                    document.querySelectorAll('.sk-panel.show').forEach(openPanel => {
                        openPanel.classList.remove('show');
                    });

                    // 2. Jika panel yang diklik tadi dalam keadaan tertutup, buka panel tersebut.
                    //    Jika sudah terbuka, langkah sebelumnya (nomor 1) sudah menutupnya.
                    if (!isCurrentlyOpen) {
                        targetPanel.classList.add('show');
                    }
                });
            });
        });
    </script>
@endpush