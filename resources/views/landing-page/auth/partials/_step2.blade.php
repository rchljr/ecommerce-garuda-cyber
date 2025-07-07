@include('landing-page.auth.partials._back_button')
<div class="w-full max-w-4xl mx-auto flex flex-col h-full">
    <div class="flex-shrink-0">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Lengkapi Data Diri Anda</h2>
    </div>

    <form action="{{ route('register.user.submit') }}" method="POST" class="flex-grow flex flex-col">
        @csrf
        <div class="flex-grow space-y-3">
            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-base font-semibold text-gray-800 mb-1">Nama Lengkap<span
                        class="text-red-600">*</span></label>
                <input type="text" id="name" name="name" value="{{ session('register.step_2.name') ?? old('name') }}"
                    class="block w-full h-12 px-4 border-2 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition @error('name') border-red-500 @else border-gray-300 @enderror"
                    placeholder="Masukkan Nama Lengkap Anda" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jabatan --}}
            <div>
                <label for="position" class="block text-base font-semibold text-gray-800 mb-1">Jabatan<small
                        class="font-normal">(Opsional)</small></label>
                <input type="text" id="position" name="position" value="{{ session('register.step_2.position') ?? old('position') }}"
                    class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition"
                    placeholder="Masukkan Jabatan Anda">
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-base font-semibold text-gray-800 mb-1">Email <span
                        class="text-red-600">*</span></label>
                <input type="email" id="email" name="email" value="{{ session('register.step_2.email') ?? old('email') }}"
                    class="block w-full h-12 px-4 border-2 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition @error('email') border-red-500 @else border-gray-300 @enderror"
                    placeholder="Masukkan Email Anda" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- No HP/Whatsapp --}}
            <div>
                <label for="phone" class="block text-base font-semibold text-gray-800 mb-1">No HP/Whatsapp <span
                        class="text-red-600">*</span></label>
                <input type="tel" id="phone" name="phone" value="{{ session('register.step_2.phone') ?? old('phone') }}"
                    class="block w-full h-12 px-4 border-2 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition @error('phone') border-red-500 @else border-gray-300 @enderror"
                    placeholder="e.g., 6281234567890" required>
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kata Sandi --}}
            <div>
                <label for="password" class="block text-base font-semibold text-gray-800 mb-1">Kata Sandi <span class="text-red-600">*</span></label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="block w-full h-12 pl-4 pr-12 border-2 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition @error('password') border-red-500 @else border-gray-300 @enderror"
                        placeholder="Masukkan Kata Sandi Anda" required>
                    <button type="button" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-500 hover:text-gray-700 toggle-password-visibility" data-target="password">
                        <svg class="eye-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg class="eye-slash-icon h-6 w-6 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59" /></svg>
                    </button>
                </div>
                <span class="text-xs text-gray-500">*Minimal 8 karakter</span>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Kata Sandi --}}
            <div>
                <label for="password_confirmation" class="block text-base font-semibold text-gray-800 mb-1">Konfirmasi Kata Sandi <span class="text-red-600">*</span></label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="block w-full h-12 pl-4 pr-12 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition"
                        placeholder="Masukkan ulang kata sandi" required>
                    <button type="button" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-500 hover:text-gray-700 toggle-password-visibility" data-target="password_confirmation">
                        <svg class="eye-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg class="eye-slash-icon h-6 w-6 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59" /></svg>
                    </button>
                </div>
            </div>
            <div class="flex-shrink-0 pt-4">
                <button type="submit"
                    class="w-full bg-red-700 text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition-colors text-lg">Lanjut</button>
            </div>
        </div>
    </form>
</div>