@extends('layouts.customer')

@section('title', 'Login')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <div>
                {{-- Anda bisa menaruh logo toko di sini --}}
                {{-- <img class="mx-auto h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo Toko"> --}}
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Login ke Akun Anda
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Atau <a href="{{ route('customer.register.form') }}"
                        class="font-medium text-gray-800 hover:text-black">Daftar akun baru</a>
                </p>
            </div>

            {{-- Menampilkan pesan error umum --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('customer.login.submit') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-gray-500 focus:border-gray-500 focus:z-10 sm:text-sm"
                            placeholder="Alamat Email" value="{{ old('email') }}">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-gray-500 focus:border-gray-500 focus:z-10 sm:text-sm"
                            placeholder="Password">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection