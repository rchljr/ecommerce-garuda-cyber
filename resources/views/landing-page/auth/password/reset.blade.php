@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="flex items-center justify-center h-full">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Atur Ulang Kata Sandi</h2>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-base font-semibold text-gray-800 mb-2">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required
                    autocomplete="email"
                    class="block w-full h-14 px-4 border-2 rounded-lg @error('email') border-red-500 @else border-gray-300 @enderror bg-gray-100 transition"
                    readonly>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-base font-semibold text-gray-800 mb-2">Password Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="block w-full h-14 px-4 border-2 rounded-lg @error('password') border-red-500 @else border-gray-300 @enderror bg-white focus:border-[#B20000] focus:ring-0 transition">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password-confirm" class="block text-base font-semibold text-gray-800 mb-2">Konfirmasi
                    Password Baru</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="block w-full h-14 px-4 border-2 border-gray-300 rounded-lg bg-white focus:border-[#B20000] focus:ring-0 transition">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-[#B20000] text-white font-semibold py-4 rounded-lg hover:bg-[#900000] transition-colors text-lg">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection