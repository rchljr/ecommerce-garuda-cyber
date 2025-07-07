@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="flex items-center justify-center h-full">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Lupa Kata Sandi?</h2>
            <p class="text-gray-600 mt-2">Masukkan email Anda dan kami akan mengirimkan link untuk mereset kata sandi
                Anda.</p>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-base font-semibold text-gray-800 mb-2">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    autofocus
                    class="block w-full h-14 px-4 border-2 rounded-lg @error('email') border-red-500 @else border-gray-300 @enderror bg-white focus:border-[#B20000] focus:ring-0 transition">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <button type="submit"
                    class="w-full bg-[#B20000] text-white font-semibold py-4 rounded-lg hover:bg-[#900000] transition-colors text-lg">
                    Kirim Link Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection