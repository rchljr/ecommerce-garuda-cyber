@extends('layouts.admin')
@section('title', 'Kelola Mitra')
@section('content')
    <div class="flex flex-col h-full">
        {{-- Header --}}
        <div class="flex-shrink-0 flex justify-between items-center mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Kelola Mitra</h1>
                <p class="text-lg text-gray-500 mt-6">Daftar mitra yang terdaftar di platform Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Form Pencarian (awalnya tersembunyi) --}}
                <form id="search-form" action="{{ route('admin.mitra.kelola') }}" method="GET" class="hidden">
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="block w-full px-4 h-10 border-2 border-gray-300 rounded-lg bg-white focus:border-red-600 focus:ring-0 transition" placeholder="Cari nama, email, toko...">
                </form>

                {{-- Tombol Ikon Pencarian --}}
                <button id="search-icon-btn" class="p-2 rounded-full hover:bg-gray-100 flex-shrink-0" title="Cari">
                    <svg width="20" height="20" fill="none">
                        <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Tabel Mitra --}}
        <div class="flex-grow overflow-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="w-full whitespace-no-wrap min-w-[1000px]">
                <thead class="bg-gray-200">
                    <tr class="text-center font-semibold text-sm uppercase text-gray-700 tracking-wider">
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Nama Toko</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Pemilik</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Kategori</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Paket</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Tanggal Berakhir</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($mitras as $mitra)
                        @php
                            $userPackage = $mitra->userPackage;
                            $expiredDate = optional($userPackage)->expired_date;
                            $statusText = 'Tidak Diketahui';
                            $statusClass = 'bg-gray-100 text-gray-700';

                            if ($userPackage) {
                                $now = \Carbon\Carbon::now();
                                if ($userPackage->status == 'active') {
                                    if ($expiredDate && \Carbon\Carbon::parse($expiredDate)->isPast()) {
                                        $statusText = 'Expired';
                                        $statusClass = 'bg-red-100 text-red-700';
                                    } elseif ($expiredDate && \Carbon\Carbon::parse($expiredDate)->isBetween($now, $now->copy()->addDays(7))) {
                                        $statusText = 'Segera Berakhir';
                                        $statusClass = 'bg-yellow-100 text-yellow-700';
                                    } else {
                                        $statusText = 'Aktif';
                                        $statusClass = 'bg-green-100 text-green-700';
                                    }
                                } else {
                                    $statusText = 'Tidak Aktif';
                                    $statusClass = 'bg-red-100 text-red-700';
                                }
                            }
                        @endphp
                        <tr class="text-gray-700 text-center">
                            <td class="px-6 py-4 text-left">
                                <div class="font-bold text-black">{{ optional($mitra->shop)->shop_name ?? 'N/A' }}</div>
                                <a href="http://{{ optional($mitra->subdomain)->subdomain_name }}.gci.com" target="_blank" class="text-sm text-gray-500 underline">
                                    {{ optional($mitra->subdomain)->subdomain_name }}.gci.com
                                </a>
                            </td>
                            <td class="px-6 py-4 text-left">
                                <div class="font-semibold text-black">{{ $mitra->name }}</div>
                                @if($mitra->phone)
                                    <a href="https://wa.me/{{ $mitra->phone }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline flex items-center gap-1.5 mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" class="text-green-500 flex-shrink-0">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.854 7.854 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                        </svg>
                                        <span>{{ $mitra->phone }}</span>
                                    </a>
                                @else
                                    <div class="text-sm text-gray-600 mt-1">N/A</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $categorySlug = optional($mitra->shop)->product_categories;
                                @endphp
                                {{-- Cari nama kategori di dalam map, jika tidak ada tampilkan slug aslinya --}}
                                {{ $categoryMap[$categorySlug] ?? $categorySlug ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ optional($userPackage->subscriptionPackage)->package_name ?? 'N/A' }}
                                <div class="text-xs text-gray-500">
                                    @if(optional($userPackage)->plan_type == 'yearly')
                                        1 Tahun
                                    @elseif(optional($userPackage)->plan_type == 'monthly')
                                        1 Bulan
                                    @else
                                        {{ ucfirst(optional($userPackage)->plan_type) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ format_tanggal($expiredDate) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-8 flex justify-center gap-3">
                                @if(optional($userPackage)->status == 'active')
                                {{-- PERUBAHAN: Menghapus onsubmit, dan menambahkan class 'deactivate-form' --}}
                                <form action="{{ route('admin.mitra.updateStatus', $mitra->id) }}" method="POST" class="deactivate-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="inactive">
                                    <button type="submit" title="Nonaktifkan" class="text-gray-600 hover:text-red-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Belum ada data mitra yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Link Paginasi --}}
        <div class="mt-4">
            {{ $mitras->links() }}
        </div>
    </div>
@endsection