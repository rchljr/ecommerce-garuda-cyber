@include('landing-page.auth.partials._back_button')
<div class="w-full max-w-4xl mx-auto flex flex-col h-full">
    <div class="flex-shrink-0">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Lengkapi Data Toko Anda</h2>
    </div>

    <form action="{{ route('register.shop.submit') }}" method="POST" enctype="multipart/form-data"
        class="flex-grow flex flex-col">
        @csrf
        <div class="flex-grow space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="shop_name" class="block text-base font-semibold text-gray-800 mb-1">Nama Toko<span
                            class="text-red-600">*</span></label>
                    <input type="text" id="shop_name" name="shop_name"
                        value="{{ session('register.step_3.shop_name') ?? old('shop_name') }}"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label for="year_founded" class="block text-base font-semibold text-gray-800 mb-1">Tahun Berdiri
                        <small>(Opsional)</small></label>
                    <input type="date" id="year_founded" name="year_founded"
                        value="{{ session('register.step_3.year_founded') ?? old('year_founded') }}"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="shop_address" class="block text-base font-semibold text-gray-800 mb-1">Alamat Lengkap
                        Toko<span class="text-red-600">*</span></label>
                    <input type="text" id="shop_address" name="shop_address"
                        value="{{ session('register.step_3.shop_address') ?? old('shop_address') }}"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label for="product_categories" class="block text-base font-semibold text-gray-800 mb-1">Kategori
                        Produk<span class="text-red-600">*</span></label>
                    <select id="product_categories" name="product_categories"
                        class="mt-1 block w-full h-12 px-4 border-2 border-gray-300 rounded-lg" required>
                        <option value="">Pilih Kategori Produk</option>
                        @isset($categories)
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" {{ (session('register.step_3.product_categories') ?? old('product_categories')) == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
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
            <div class="flex-shrink-0 pt-4">
                <button type="submit"
                    class="w-full bg-red-700 text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition-colors text-lg">
                    @if(isset($isBusinessPlan) && $isBusinessPlan)
                        Lanjut
                    @else
                        Daftar
                    @endif
                </button>
            </div>
        </div>
    </form>
</div>