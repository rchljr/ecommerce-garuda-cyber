@extends('layouts.customer')
@section('title', 'Profil Saya')

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
                    <h1 class="text-2xl md:text-3xl font-bold mb-6">Profil Saya</h1>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('tenant.account.profile.update', ['subdomain' => $currentSubdomain]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8">
                            <!-- Kolom Kiri Form -->
                            <div class="space-y-6">
                                <div class="mb-6">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="name" id="name" value="{{ $user->name }}"
                                        class="mt-1 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('name')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-6">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ $user->email }}"
                                        class="mt-1 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('email')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-6">
                                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal
                                        Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                        value="{{ $user->tanggal_lahir }}"
                                        class="mt-1 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('tanggal_lahir')<span
                                    class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-6">
                                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis
                                        Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin"
                                        class="mt-1 mb-6 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="" disabled {{ is_null($user->jenis_kelamin) ? 'selected' : '' }}>
                                            Pilih jenis kelamin</option>
                                        <option value="pria" {{ $user->jenis_kelamin == 'pria' ? 'selected' : '' }}>Pria</option>
                                        <option value="wanita" {{ $user->jenis_kelamin == 'wanita' ? 'selected' : '' }}>Wanita</option>
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                    <input type="tel" name="phone" id="phone" value="{{$user->phone }}"
                                        class="mt-1 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">
                                </div>
                            </div>

                            <!-- Kolom Kanan Form -->
                            <div class="space-y-6">
                                <div class="mb-6">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password
                                        Baru</label>
                                    <input type="password" name="password" id="password"
                                        class="mt-1 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="********">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                                    @error('password')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                </div>
                                <div class="mb-6">
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="mt-1 block w-full h-12 px-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="********">
                                </div>
                                <div class="mb-6">
                                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <textarea name="alamat" id="alamat" rows="4"
                                        class="mt-1 block w-full p-4 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('alamat', $user->alamat) }}</textarea>
                                </div>
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700">Foto Profil</label>
                                    <div class="mt-2 flex items-center">
                                        <span class="inline-block h-24 w-24 rounded-full overflow-hidden bg-gray-100">
                                            <img id="photo-preview"
                                                src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://placehold.co/96x96/EBF4FF/76A9FA?text=' . strtoupper(substr($user->name, 0, 1)) }}"
                                                alt="Foto Profil" class="h-full w-full object-cover">
                                        </span>
                                        <input type="file" name="photo" id="photo" class="hidden"
                                            onchange="document.getElementById('photo-preview').src = window.URL.createObjectURL(this.files[0])">
                                        <label for="photo"
                                            class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 cursor-pointer">
                                            Pilih Gambar
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Ukuran gambar maks. 2MB. Format: JPEG, PNG.</p>
                                    @error('photo')<span class="text-red-500 text-sm mt-1">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 pt-5 border-t border-gray-200">
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="bg-gray-800 text-white font-semibold py-2 px-6 rounded-lg hover:bg-gray-700 transition-colors">Simpan
                                    Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
@endsection