@extends('layouts.customer')
@section('title', 'Poin Saya')

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp

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
                            <h1 class="text-2xl md:text-3xl font-bold">Poin Saya</h1>
                            <p class="text-lg mt-2 font-bold text-red-600">{{ number_format($user->points) }} Poin</p>
                        </div>
                        <form action="#" method="POST" class="flex items-center w-full sm:w-auto">
                            <button type="button"
                                class="bg-red-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-red-700">Dapatkan
                                Poin</button>
                        </form>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Daftar Hadiah -->
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        @forelse ($rewards as $reward)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm flex items-center p-4 gap-4">
                                <img src="{{ $reward->image ?? 'https://placehold.co/80x80/f1f5f9/cbd5e1?text=Hadiah' }}"
                                    alt="{{ $reward->name }}" class="w-20 h-20 rounded-md object-cover flex-shrink-0">
                                <div class="flex-grow">
                                    <h3 class="font-bold text-gray-800">{{ $reward->name }}</h3>
                                    <p class="text-sm font-bold text-red-600 mt-1">{{ number_format($reward->points_required) }}
                                        Poin</p>
                                </div>
                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <button class="text-gray-400 hover:text-gray-600" title="{{ $reward->description }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <form
                                        action="{{ route('tenant.account.points.redeem', ['subdomain' => $currentSubdomain, 'rewardId' => $reward->id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Tukarkan {{ $reward->points_required }} poin dengan {{ $reward->name }}?')">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 text-white text-sm font-semibold px-6 py-2 rounded-lg hover:bg-red-700">Tukar</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="xl:col-span-2 text-center py-12 text-gray-500">
                                <p>Belum ada hadiah yang bisa ditukarkan saat ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection