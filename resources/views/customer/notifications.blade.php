@extends('layouts.customer')
@section('title', 'Notifikasi Saya')

@section('content')
    <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8">
            <aside class="w-full md:w-1/4 lg:w-1/5 flex-shrink-0">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    @include('layouts._partials.customer-sidebar')
                </div>
            </aside>

            <main class="w-full md:w-3/4 lg:w-4/5">
                <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold">Aktivitas & Notifikasi</h1>
                            <p class="text-gray-500 mt-1">Semua aktivitas dan pemberitahuan untuk akun Anda.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse ($notifications as $notification)
                            @php
                                // Logika untuk ikon dan warna berdasarkan tipe notifikasi
                                $icon_color = 'bg-gray-100';
                                $icon_svg = '<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>';

                                if ($notification->status == 'success') {
                                    $icon_color = 'bg-green-100';
                                    $icon_svg = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                } elseif (in_array($notification->status, ['cancelled', 'failed'])) {
                                    $icon_color = 'bg-red-100';
                                    $icon_svg = '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                } elseif ($notification->status == 'profile') {
                                    $icon_color = 'bg-blue-100';
                                    $icon_svg = '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
                                }
                            @endphp

                            <a href="{{ $notification->link ?? '#' }}"
                                class="block p-4 rounded-lg bg-white hover:bg-gray-50 border">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <span class="flex items-center justify-center h-12 w-12 rounded-lg {{ $icon_color }}">
                                            {!! $icon_svg !!}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $notification->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($notification->date)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <p class="mt-4">Belum ada aktivitas atau notifikasi baru.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection