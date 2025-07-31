@extends('layouts.mitra')
@section('title', 'Pengaturan Tema Toko')

@section('content')
    <div class="bg-gray-100 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="container mx-auto max-w-4xl">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Pengaturan Tema Toko</h1>
                <p class="text-gray-500 mt-1">Sesuaikan tampilan toko online Anda di sini.</p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Sukses</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- Menampilkan error validasi umum jika ada --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Ada Kesalahan!</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-md">
                <form action="{{ route('mitra.tema.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{--
                    PENTING: Pastikan variabel $tema dan $tenant dilewatkan dari controller
                    (misalnya dari method `editor` atau `create` di TemaController Anda).

                    Contoh di controller:
                    public function editor(Template $template) {
                    $user = Auth::user();
                    $tenant = $user->tenant; // Dapatkan tenant
                    $shop = $user->shop; // Dapatkan shop
                    $tema = CustomTema::firstOrNew(['user_id' => $user->id]);

                    return view('dashboard-mitra.editor.index', compact('tema', 'shop', 'tenant', 'template'));
                    // 'template' di sini adalah objek Template yang dipilih
                    }
                    --}}


                    {{-- HIDDEN INPUT UNTUK template_name --}}
                    {{-- Kita asumsikan $tenant->template->name sudah tersedia karena dipilih di halaman sebelumnya.
                    Jika $tenant atau $tenant->template bisa null, tambahkan ?? '' untuk fallback. --}}
                    <input type="hidden" name="template_name" value="{{ optional($tenant->template)->name ?? '' }}">

                    {{-- HIDDEN INPUT UNTUK user_id (opsional, karena sudah diambil dari Auth::id() di controller) --}}
                    {{-- Ini bisa membantu jika Anda ingin melacak user ID di request langsung --}}
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">


                    <div class="space-y-6">

                        <div>
                            <label for="shop_name" class="block text-sm font-medium text-gray-700">Nama Toko</label>
                            <input type="text" name="shop_name" id="shop_name"
                                value="{{ old('shop_name', optional($tema)->shop_name ?? optional($shop)->shop_name ?? '') }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="Contoh: Toko Baju Keren">
                            @error('shop_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="shop_description" class="block text-sm font-medium text-gray-700">Deskripsi Singkat
                                Toko</label>
                            <textarea id="shop_description" name="shop_description" rows="3"
                                class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                placeholder="Jelaskan secara singkat tentang toko Anda, produk, atau layanan">{{ old('shop_description', optional($tema)->shop_description ?? '') }}</textarea>
                            @error('shop_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Logo Toko</label>
                            <div class="mt-1 flex items-center">
                                @if(optional($tema)->shop_logo) {{-- Gunakan optional() di sini --}}
                                    <img src="{{ asset('storage/' . $tema->shop_logo) }}" alt="Logo Saat Ini"
                                        class="h-16 w-16 rounded-full object-cover border border-gray-200 shadow-sm">
                                @else
                                    <span
                                        class="inline-block h-16 w-16 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shadow-sm">
                                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                @endif
                                <input type="file" name="shop_logo" class="ml-5 text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100">
                            </div>
                            @error('shop_logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700">Warna
                                    Primer</label>
                                <input type="color" id="primary_color" name="primary_color"
                                    value="{{ old('primary_color', optional($tema)->primary_color ?? '#4F46E5') }}"
                                    class="mt-1 h-10 w-full block border border-gray-300 rounded-md cursor-pointer"> {{--
                                Tambahkan cursor-pointer --}}
                                @error('primary_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700">Warna
                                    Sekunder</label>
                                <input type="color" id="secondary_color" name="secondary_color"
                                    value="{{ old('secondary_color', optional($tema)->secondary_color ?? '#D946EF') }}"
                                    class="mt-1 h-10 w-full block border border-gray-300 rounded-md cursor-pointer"> {{--
                                Tambahkan cursor-pointer --}}
                                @error('secondary_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-5">
                        <div class="flex justify-end">
                            <button type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection