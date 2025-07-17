@extends('layouts.mitra')

@section('title', 'Edit Informasi Kontak')

@section('content')
    <div class="bg-white shadow-xl rounded-xl p-6 sm:p-8 md:p-10 max-w-screen-lg w-full mx-auto">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Edit Informasi Kontak</h1>

        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative mb-6 text-center shadow-sm">
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">
                <strong class="font-bold">Oops!</strong> Ada masalah dengan input Anda:
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mitra.contacts.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <!-- Address Line 1 -->
                <div class="col-span-1 md:col-span-2">
                    <label for="address_line1" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Baris
                        1:</label>
                    <input type="text" name="address_line1" id="address_line1"
                        value="{{ old('address_line1', $contact->address_line1) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address_line1') border-red-500 @enderror"
                        placeholder="Contoh: Jl. Sudirman No. 123">
                    @error('address_line1')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Line 2 -->
                <div class="col-span-1 md:col-span-2">
                    <label for="address_line2" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Baris 2
                        (Opsional):</label>
                    <input type="text" name="address_line2" id="address_line2"
                        value="{{ old('address_line2', $contact->address_line2) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address_line2') border-red-500 @enderror"
                        placeholder="Contoh: Gedung A, Lantai 5">
                    @error('address_line2')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">Kota:</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $contact->city) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('city') border-red-500 @enderror"
                        placeholder="Contoh: Jakarta">
                    @error('city')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- State -->
                <div>
                    <label for="state" class="block text-sm font-semibold text-gray-700 mb-1">Provinsi:</label>
                    <input type="text" name="state" id="state" value="{{ old('state', $contact->state) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('state') border-red-500 @enderror"
                        placeholder="Contoh: DKI Jakarta">
                    @error('state')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Postal Code -->
                <div>
                    <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-1">Kode Pos:</label>
                    <input type="text" name="postal_code" id="postal_code"
                        value="{{ old('postal_code', $contact->postal_code) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('postal_code') border-red-500 @enderror"
                        placeholder="Contoh: 12345">
                    @error('postal_code')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Telepon:</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $contact->phone) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-500 @enderror"
                        placeholder="Contoh: +62 812 3456 7890">
                    @error('phone')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email:</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $contact->email) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                        placeholder="Contoh: info@toko.com">
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website -->
                <div>
                    <label for="website" class="block text-sm font-semibold text-gray-700 mb-1">Website (Opsional):</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $contact->website) }}"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('website') border-red-500 @enderror"
                        placeholder="Contoh: https://www.toko.com">
                    @error('website')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Social Media Links (Full Width) -->
                <div class="col-span-1 md:col-span-2 mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Media Sosial</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="facebook_url" class="block text-sm font-semibold text-gray-700 mb-1">Facebook
                                URL:</label>
                            <input type="url" name="facebook_url" id="facebook_url"
                                value="{{ old('facebook_url', $contact->facebook_url) }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('facebook_url') border-red-500 @enderror"
                                placeholder="https://facebook.com/toko">
                            @error('facebook_url')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="twitter_url" class="block text-sm font-semibold text-gray-700 mb-1">Twitter
                                URL:</label>
                            <input type="url" name="twitter_url" id="twitter_url"
                                value="{{ old('twitter_url', $contact->twitter_url) }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('twitter_url') border-red-500 @enderror"
                                placeholder="https://twitter.com/toko">
                            @error('twitter_url')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="instagram_url" class="block text-sm font-semibold text-gray-700 mb-1">Instagram
                                URL:</label>
                            <input type="url" name="instagram_url" id="instagram_url"
                                value="{{ old('instagram_url', $contact->instagram_url) }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('instagram_url') border-red-500 @enderror"
                                placeholder="https://instagram.com/toko">
                            @error('instagram_url')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pinterest_url" class="block text-sm font-semibold text-gray-700 mb-1">Pinterest
                                URL:</label>
                            <input type="url" name="pinterest_url" id="pinterest_url"
                                value="{{ old('pinterest_url', $contact->pinterest_url) }}"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('pinterest_url') border-red-500 @enderror"
                                placeholder="https://pinterest.com/toko">
                            @error('pinterest_url')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Working Hours -->
                <div class="col-span-1 md:col-span-2">
                    <label for="working_hours" class="block text-sm font-semibold text-gray-700 mb-1">Jam Kerja:</label>
                    <textarea name="working_hours" id="working_hours" rows="3"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('working_hours') border-red-500 @enderror"
                        placeholder="Contoh: Senin-Jumat: 09:00 - 17:00">{{ old('working_hours', $contact->working_hours) }}</textarea>
                    @error('working_hours')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Map Embed Code -->
                <div class="col-span-1 md:col-span-2">
                    <label for="map_embed_code" class="block text-sm font-semibold text-gray-700 mb-1">Kode Embed Peta
                        (Iframe Google Maps):</label>
                    <textarea name="map_embed_code" id="map_embed_code" rows="5"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('map_embed_code') border-red-500 @enderror"
                        placeholder="Paste kode iframe Google Maps di sini...">{{ old('map_embed_code', $contact->map_embed_code) }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">Anda bisa mendapatkan kode ini dari Google Maps, klik 'Share' ->
                        'Embed a map' -> 'COPY HTML'.</p>
                    @error('map_embed_code')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 mt-10">
                <a href="#" onclick="window.history.back(); return false;"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                    Simpan Informasi Kontak
                </button>
                <a href="{{ route('mitra.editor.edit') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300">
                    Kembali ke Editor
                </a>
            </div>
        </form>
    </div>
@endsection
