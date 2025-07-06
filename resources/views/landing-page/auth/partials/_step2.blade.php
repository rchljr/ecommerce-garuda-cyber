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
                <label for="password" class="block text-base font-semibold text-gray-800 mb-1">Kata Sandi <span
                        class="text-red-600">*</span></label>
                <input type="password" id="password" name="password"
                    class="block w-full h-12 px-4 border-2 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition @error('password') border-red-500 @else border-gray-300 @enderror"
                    placeholder="Masukkan Kata Sandi Anda" required>
                <span class="text-xs text-gray-500">*Minimal 8 karakter</span>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Kata Sandi --}}
            <div>
                <label for="password_confirmation" class="block text-base font-semibold text-gray-800 mb-1">Konfirmasi
                    Kata Sandi <span class="text-red-600">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition"
                    placeholder="Masukkan ulang kata sandi" required>
            </div>
            <div class="flex-shrink-0 pt-4">
                <button type="submit"
                    class="w-full bg-red-700 text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition-colors text-lg">Lanjut</button>
            </div>
        </div>
    </form>
</div>