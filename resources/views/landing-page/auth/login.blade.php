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
                
                {{-- Menampilkan pesan sukses dari reset password --}}
                @if (session('status'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        {{ session('status') }}
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
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-base font-semibold text-gray-800">Kata Sandi</label>
                        {{-- <a href="{{ route('password.request') }}" class="text-sm font-semibold text-red-600 hover:underline">
                            Lupa Kata Sandi?
                        </a> --}}
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="block w-full h-14 pl-4 pr-12 border-2 border-gray-300 rounded-lg bg-white focus:border-[#B20000] focus:ring-0 transition"
                            placeholder="Masukkan Kata Sandi" required>
                        
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-500 hover:text-gray-700">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeSlashIcon = document.getElementById('eye-slash-icon');

        togglePassword.addEventListener('click', function () {
            // Ganti tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Ganti ikon yang ditampilkan
            eyeIcon.classList.toggle('hidden');
            eyeSlashIcon.classList.toggle('hidden');
        });
    });
</script>
@endpush
