
@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="flex items-center justify-center h-full">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Login</h2>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                @csrf

                {{-- Menampilkan pesan error umum --}}
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-base font-semibold text-gray-800 mb-2">Email</label>
                    <input type="email" id="email" name="email"
                        class="block w-full h-14 px-4 border-2 border-gray-300 rounded-lg bg-white focus:border-[#B20000] focus:ring-0 transition"
                        placeholder="Masukkan Email Anda" required value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center mb-2">
                        <label for="password" class="block text-base font-semibold text-gray-800">Kata Sandi</label>
                    </div>
                    <input type="password" id="password" name="password"
                        class="block w-full h-14 px-4 border-2 border-gray-300 rounded-lg bg-white focus:border-[#B20000] focus:ring-0 transition"
                        placeholder="Masukkan Kata Sandi" required>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                        {{-- <a href="#" class="flex justify-end mt-2 text-sm font-semibold text-red-600 hover:underline">Lupa Kata Sandi?</a> --}}
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-[#B20000] text-white font-semibold py-4 rounded-lg hover:bg-[#900000] transition-colors text-lg">
                        Login
                    </button>
                </div>

                <p class="text-center text-gray-600">
                    Belum punya akun? <a href="{{ route('register.form') }}"
                        class="font-semibold text-red-600 hover:underline">Daftar di sini</a>
                </p>
            </form>
        </div>
    </div>
@endsection