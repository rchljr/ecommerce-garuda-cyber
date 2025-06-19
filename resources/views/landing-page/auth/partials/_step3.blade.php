<div class="w-full mx-auto flex flex-col h-full">
    <div class="flex-shrink-0">
        <a href="{{ route('register.form', ['step' => 2]) }}"
            class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Lengkapi Data Toko Anda</h2>
    </div>

    <form action="{{ route('register.step3') }}" method="POST" enctype="multipart/form-data"
        class="flex-grow flex flex-col">
        @csrf
        <div class="flex-grow space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="shop_name" class="block text-base font-semibold text-gray-800 mb-1">Nama Toko<span
                            class="text-red-600">*</span></label>
                    <input type="text" id="shop_name" name="shop_name" placeholder="Masukkan Nama Toko Anda"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition"
                        required>
                </div>
                <div>
                    <label for="year_founded" class="block text-base font-semibold text-gray-800 mb-1">Tahun Berdiri
                        <small class="font-normal">(Opsional)</small></label>
                    <input type="date" id="year_founded" name="year_founded" placeholder="Tahun Berdiri Toko"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition">
                </div>
                <div>
                    <label for="shop_address" class="block text-base font-semibold text-gray-800 mb-1">Alamat Lengkap
                        Toko<span class="text-red-600">*</span></label>
                    <input type="text" id="shop_address" name="shop_address" placeholder="Masukkan Alamat Toko Anda"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition"
                        required>
                </div>
                <div>
                    <label for="product_categories" class="block text-base font-semibold text-gray-800 mb-1">Kategori
                        Produk<span class="text-red-600">*</span></label>
                    <select id="product_categories" name="product_categories"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white hover:border-red-600 focus:border-red-600 focus:ring-0 transition"
                        required>
                        <option value="">Pilih Kategori Produk</option>
                        @isset($categories)
                            @foreach ($categories as $category)
                                {{-- Menggunakan slug agar lebih ramah URL --}}
                                <option value="{{ $category->slug }}">{{ $category->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @include('landing-page.auth.partials._file-input', ['name' => 'shop_photo', 'label1' => 'Foto Tempat Usaha', 'required' => true])
                @include('landing-page.auth.partials._file-input', ['name' => 'ktp', 'label1' => 'Scan/Foto KTP', 'required' => true])
                @include('landing-page.auth.partials._file-input', ['name' => 'sku', 'label2' => 'Surat Keterangan Usaha (SKU)'])
                @include('landing-page.auth.partials._file-input', ['name' => 'npwp', 'label2' => 'Scan/Foto NPWP'])
                @include('landing-page.auth.partials._file-input', ['name' => 'nib', 'label2' => 'Nomor Induk Usaha (NIB)'])
                @include('landing-page.auth.partials._file-input', ['name' => 'iumk', 'label2' => 'Surat Izin Usaha Mikro dan Kecil (IUMK)'])
            </div>
        </div>
        <div class="flex-shrink-0 pt-4">
            <button type="submit"
                class="w-full bg-red-700 text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition-colors text-lg">Daftar</button>
        </div>
    </form>
</div>