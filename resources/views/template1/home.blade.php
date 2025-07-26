{{-- resources/views/template1/home.blade.php --}}
@extends('template1.layouts.template')

@section('title', 'Beranda')

@push('styles')
    {{-- Style kustom untuk modal varian dan notifikasi --}}
    <style>
        /* PERBAIKAN: Menambahkan text-shadow untuk keterbacaan */
        .hero__text h6,
        .hero__text h2,
        .hero__text p {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
        }

        .banner__item__text h2 {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 2rem;
            font-weight: bold;
            color: #888;
            cursor: pointer;
        }

        .modal-variant-select {
            width: 100%;
            height: 46px;
            border: 1px solid #e1e1e1;
            padding: 0 15px;
            font-size: 14px;
            color: #444444;
            border-radius: 5px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .modal-variant-select:disabled {
            background-color: #f3f3f3;
            color: #b0b0b0;
            cursor: not-allowed;
        }

        .primary-btn:disabled {
            background-color: #b0b0b0;
            cursor: not-allowed;
        }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #2c3e50;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.4s cubic-bezier(0.215, 0.610, 0.355, 1);
        }

        .toast-notification.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .toast-notification.success {
            background-color: #27ae60;
        }

        .toast-notification.error {
            background-color: #c0392b;
        }

        .filter__controls li {
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .filter__controls li.active {
            background-color: #111111;
            color: #ffffff;
        }

        /* Hapus atau modifikasi style ini jika Anda tidak lagi ingin hover icons */
        /* Anda bisa menghapus semua .product__hover terkait jika tidak dibutuhkan */
        .product__hover {
            display: none; /* Menyembunyikan ikon hover jika ingin benar-benar hilang */
        }
        /* Atau biarkan jika Anda punya fungsi lain dengan itu */

        .product__hover li i {
            color: #111;
            font-size: 18px;
            background: #fff;
            display: block;
            height: 45px;
            width: 45px;
            line-height: 45px;
            text-align: center;
            border-radius: 50%;
            transition: all .3s;
        }

        .product__hover li:hover i {
            background: #ca1515;
            color: #fff;
            transform: rotate(360deg);
        }

        .product__hover a.toggle-wishlist.active i,
        #modal-wishlist-btn.active i {
            background: #ca1515;
            color: #fff;
        }

        /* Jika Anda ingin area gambar bisa diklik tapi tidak ada icon,
           pastikan link overlaynya mengisi seluruh area gambar */
        .product__item__pic a.product-image-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5; /* Pastikan di atas gambar tapi di bawah label seperti "Baru" */
            display: block; /* Agar link memenuhi seluruh area */
        }

        /* Agar tombol "Add to Cart" tidak ditampilkan */
        .product__item__text .add-cart {
            display: none; /* Menyembunyikan tombol keranjang di halaman utama */
        }

    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $subdomainName = request()->route('subdomain');
    @endphp

    <section class="hero">
        <div class="hero__slider owl-carousel">
            @forelse ($heroes as $hero)
                <div class="hero__items set-bg" data-setbg="{{ $hero->image_url }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    <h6>{{ $hero->subtitle }}</h6>
                                    <h2>{{ $hero->title }}</h2>
                                    <p>{{ $hero->description }}</p>
                                    <a href="{{ $hero->button_url }}" class="primary-btn">{{ $hero->button_text }} <span
                                            class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="hero__items set-bg" data-setbg="{{ asset('template1/img/hero/hero-default.jpg') }}">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-5 col-lg-7 col-md-8">
                                <div class="hero__text">
                                    <h6>Selamat Datang!</h6>
                                    <h2>Toko Fashion Terbaik Anda</h2>
                                    <p>Atur tampilan hero section Anda melalui dashboard editor.</p>
                                    <a href="#" class="primary-btn">Telusuri Sekarang <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
     <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                @forelse ($banners as $banner)
                    <div
                        class="{{ $loop->first && $banners->count() > 1 ? 'col-lg-7 offset-lg-4' : ($loop->index === 1 && $banners->count() > 2 ? 'col-lg-5' : 'col-lg-7') }}">
                        <div
                            class="banner__item {{ $loop->index === 1 ? 'banner__item--middle' : ($loop->last && $banners->count() > 1 ? 'banner__item--last' : '') }}">
                            <div class="banner__item__pic">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                            </div>
                            <div class="banner__item__text">
                                @if ($banner->title)
                                    <h2>{{ $banner->title }}</h2>
                                @endif
                                @if ($banner->link_url)
                                    <a href="{{ $banner->link_url }}">{{ $banner->button_text ?? 'Shop now' }}</a>
                                @else
                                    <p>{{ $banner->button_text ?? '' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Konten default jika tidak ada banner yang diatur --}}
                    <div class="col-lg-7 offset-lg-4">
                        <div class="banner__item">
                            <div class="banner__item__pic">
                                <img src="{{ asset('template1/img/banner/banner-1.jpg') }}" alt="Default Banner 1">
                            </div>
                            <div class="banner__item__text">
                                <h2>Banner1</h2>
                                <a href="#">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="banner__item banner__item--middle">
                            <div class="banner__item__pic">
                                <img src="{{ asset('template1/img/banner/banner-2.jpg') }}" alt="Default Banner 2">
                            </div>
                            <div class="banner__item__text">
                                <h2>Banner2</h2>
                                <a href="#">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="banner__item banner__item--last">
                            <div class="banner__item__pic">
                                <img src="{{ asset('template1/img/banner/banner-3.jpg') }}" alt="Default Banner 3">
                            </div>
                            <div class="banner__item__text">
                                <h2>Banner3</h2>
                                <a href="#">Shop now</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="active" data-filter=".best-seller">Produk Terlaris</li>
                        <li data-filter=".new-arrival">Koleksi Terbaru</li>
                        <li data-filter=".hot-sale">Promo Spesial</li>
                    </ul>
                </div>
            </div>

            <div class="row product__filter">
                @forelse ($allProducts as $product)
                    @php
                        $classes = '';
                        if (($bestSellers ?? collect())->contains($product)) { $classes .= ' best-seller'; }
                        if (($newArrivals ?? collect())->contains($product)) { $classes .= ' new-arrival'; }
                        if (($hotSales ?? collect())->contains($product)) { $classes .= ' hot-sale'; }
                    @endphp
                    <div class="col-lg-3 col-md-6 col-sm-6 mix{{ $classes }}">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                @if ($product->is_new_arrival)
                                    <span class="label">Baru</span>
                                @elseif($product->is_hot_sale)
                                    <span class="label">Diskon</span>
                                @endif
                                {{-- Menghapus product__hover untuk menghilangkan wishlist & eye icon --}}
                                <ul class="product__hover" style="display: none;">
                                    {{-- Konten di sini sekarang disembunyikan via style --}}
                                </ul>

                                {{-- PERBAIKAN: Bungkus div set-bg dengan tag <a> untuk link ke detail produk --}}
                                {{-- Menggunakan absolute positioning untuk link agar menutupi seluruh gambar --}}
                                <a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $subdomainName, 'product' => $product->slug]) : '#' }}"
                                   class="product-image-link" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 5;">
                                   {{-- Konten di dalam link ini bisa kosong atau alt-text untuk SEO --}}
                                   <span class="sr-only">{{ $product->name }} details</span>
                                </a>
                            </div>
                            <div class="product__item__text">
                                <h6>{{ $product->name }}</h6>
                                {{-- PERBAIKAN: Tombol Add to Cart dihapus dari sini --}}
                                {{-- <button class="add-cart open-variant-modal" data-product-id="{{ $product->id }}">
                                    + Keranjang
                                </button> --}}
                                <div class="rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa {{ ($product->rating_product ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </div>
                                <h5>{{ format_rupiah($product->price) }}</h5>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center py-5">Belum ada produk unggulan saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    {{-- Modal Varian TIDAK PERLU DIHAPUS, karena mungkin digunakan di halaman lain (misal: detail produk) --}}
    {{-- atau jika Anda ingin mengaktifkannya lagi di masa depan. Cukup pastikan tidak ada yang memicu membukanya di halaman ini. --}}
    <div id="variant-modal" class="modal-overlay">
        <div class="modal-content relative">
            <span class="modal-close">&times;</span>
            <div id="modal-body">
                {{-- Konten modal akan diisi oleh JavaScript --}}
            </div>
        </div>
    </div>

    {{-- Notifikasi Toast --}}
    <div id="toast-notification" class="toast-notification"></div>
@endsection

@push('scripts')
    <script>
        const allProductData = @json($allProducts->mapWithKeys(function ($product) {
            return [$product->id => $product];
        }));

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('variant-modal');
            const modalBody = document.getElementById('modal-body');
            const toastElement = document.getElementById('toast-notification');
            let toastTimeout;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const isPreview = {{ $isPreview ? 'true' : 'false' }};
            const subdomain = '{{ $subdomainName }}';

            // Pastikan Axios tersedia sebelum melanjutkan
            if (typeof axios === 'undefined') {
                console.error('Axios library is not loaded. AJAX requests will fail.');
                return; // Hentikan eksekusi jika axios tidak ada
            }

            // Konfigurasi Axios untuk mengirim CSRF token secara otomatis di setiap request.
            if (csrfToken) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            } else {
                console.error('CSRF Token not found. AJAX requests will fail.');
            }

            // ===================================================================
            // FUNGSI BANTUAN (HELPERS)
            // ===================================================================

            function showToast(message, type = 'success') {
                clearTimeout(toastTimeout);
                toastElement.textContent = message;
                toastElement.className = 'toast-notification'; // Reset class
                toastElement.classList.add(type, 'show');
                toastTimeout = setTimeout(() => {
                    toastElement.classList.remove('show');
                }, 3000);
            }

            function closeModal() {
                modal.classList.remove('show');
            }

            // Helper function to get an option value from a varian's options_data array
            // varian: objek varian dari product.varians
            // optionName: string, e.g., 'Size', 'Color'
            function getOptionValue(varian, optionName) {
                if (!varian || !varian.options_data || !Array.isArray(varian.options_data)) {
                    // console.warn(`Varian atau options_data tidak valid untuk ${optionName}:`, varian);
                    return null;
                }
                const option = varian.options_data.find(opt => opt.name && opt.name.toLowerCase() === optionName.toLowerCase());
                // if (!option) {
                //     console.warn(`Opsi '${optionName}' tidak ditemukan di options_data untuk varian:`, varian.options_data);
                // }
                return option ? option.value : null;
            }

            // ===================================================================
            // LOGIKA UTAMA MODAL VARIAN (UNTUK OPSI FLEKSIBEL)
            // ===================================================================

            // Objek untuk menyimpan status pilihan saat ini dari semua dropdown opsi
            let selectedOptions = {};
            // Array untuk menyimpan daftar nama opsi (misal: ['Size', 'Color', 'Material'])
            let optionNamesOrder = [];
            // Variabel untuk menyimpan varian yang saat ini cocok dengan pilihan dropdown
            let currentMatchingVarian = null;

            function populateAndShowModal(product) {
                // Reset status saat modal dibuka
                selectedOptions = {};
                optionNamesOrder = [];
                currentMatchingVarian = null;

                // Identifikasi semua nama opsi unik yang ada di produk ini (misal: 'Size', 'Color')
                if (product.varians && Array.isArray(product.varians)) {
                    product.varians.forEach(varian => {
                        if (varian.options_data && Array.isArray(varian.options_data)) {
                            varian.options_data.forEach(option => {
                                if (option.name && !optionNamesOrder.includes(option.name)) {
                                    optionNamesOrder.push(option.name);
                                }
                            });
                        }
                    });
                }
                // Urutkan nama opsi jika ada urutan preferensi (opsional, misal: Size dulu baru Color)
                // Jika tidak diurutkan, akan muncul sesuai urutan pertama kali ditemukan
                optionNamesOrder.sort((a, b) => {
                    const order = ['Size', 'Ukuran', 'Color', 'Warna']; // Prioritaskan 'Size'/'Ukuran' dan 'Color'/'Warna'
                    let indexA = order.indexOf(a);
                    let indexB = order.indexOf(b);
                    if (indexA === -1) indexA = 999; // Jika tidak ada di order, berikan nilai besar
                    if (indexB === -1) indexB = 999;
                    return indexA - indexB;
                });

                let optionsHtml = '';
                optionNamesOrder.forEach(optionName => {
                    optionsHtml += `
                        <div class="modal-variant-option-group">
                            <label for="select-${optionName.toLowerCase()}" class="block text-sm font-medium text-gray-700 mb-1">${optionName}</label>
                            <select id="select-${optionName.toLowerCase()}" name="option_${optionName.toLowerCase()}" class="modal-variant-select" required data-option-name="${optionName}" disabled>
                                <option value="">Pilih ${optionName}</option>
                            </select>
                        </div>
                    `;
                });

                // PERBAIKAN: Pastikan product.price diconvert ke number sebelum diformat
                const initialDisplayPrice = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(parseFloat(product.price) || 0); // Menggunakan parseFloat() dan fallback ke 0

                modalBody.innerHTML = `
                    <div>
                        <h4 class="font-bold text-lg">${product.name}</h4>
                        <p id="modal-display-price" class="text-red-600 font-bold text-lg">${initialDisplayPrice}</p>
                    </div>
                    <form id="modal-cart-form" class="space-y-4 mt-4" novalidate>
                        <input type="hidden" name="product_id" value="${product.id}">
                        ${optionsHtml}
                        <div class="mt-6">
                            <label for="quantity-input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" name="quantity" id="quantity-input" value="1" min="1" class="w-full border-gray-300 rounded-md" required>
                        </div>
                        <div class="flex items-center space-x-2 mt-4">
                            <button type="submit" id="modal-add-btn" class="primary-btn w-full !bg-gray-800 !text-white hover:!bg-black" disabled>Tambah ke Keranjang</button>
                        </div>
                    </form>
                `;
                modal.classList.add('show');
                attachDynamicOptionEventListeners(product);
                updateAllOptionDropdowns(product); // Panggil ini untuk mengisi dropdown pertama
            }

            function attachDynamicOptionEventListeners(product) {
                const form = modalBody.querySelector('#modal-cart-form');
                const modalAddBtn = modalBody.querySelector('#modal-add-btn');
                const priceDisplay = modalBody.querySelector('#modal-display-price');

                optionNamesOrder.forEach((optionName, index) => {
                    const selectElement = modalBody.querySelector(`#select-${optionName.toLowerCase()}`);
                    if (selectElement) {
                        selectElement.addEventListener('change', () => {
                            selectedOptions[optionName] = selectElement.value;
                            // Jika ada opsi yang tidak dipilih, hapus pilihan dari opsi-opsi berikutnya
                            for (let i = index + 1; i < optionNamesOrder.length; i++) {
                                selectedOptions[optionNamesOrder[i]] = ''; // Reset
                            }
                            updateAllOptionDropdowns(product, index + 1); // Perbarui dropdown berikutnya
                            updateButtonAndPriceState(product);
                        });
                    }
                });

                form.addEventListener('submit', handleAddToCart);
            }

            function updateAllOptionDropdowns(product, startIndex = 0) {
                if (!product.varians || !Array.isArray(product.varians)) {
                     console.warn('product.varians is not an array or is null/undefined.', product.varians);
                     return;
                }

                for (let i = startIndex; i < optionNamesOrder.length; i++) {
                    const currentOptionName = optionNamesOrder[i];
                    const selectElement = modalBody.querySelector(`#select-${currentOptionName.toLowerCase()}`);

                    if (!selectElement) {
                        console.warn(`Select element for ${currentOptionName} not found.`);
                        continue;
                    }

                    let availableValues = [];
                    // Filter varian yang cocok dengan pilihan sebelumnya
                    const filteredVarians = product.varians.filter(varian => {
                        // Untuk setiap varian, cek apakah opsi yang sudah dipilih cocok
                        let matchesPreviousOptions = true;
                        for (let j = 0; j < i; j++) { // Hanya cek opsi sebelum currentOptionName
                            const prevOptionName = optionNamesOrder[j];
                            const selectedValue = selectedOptions[prevOptionName];
                            // Cek apakah ada selectedValue (tidak kosong) dan apakah tidak cocok dengan varian ini
                            if (selectedValue && getOptionValue(varian, prevOptionName) !== selectedValue) {
                                matchesPreviousOptions = false;
                                break;
                            }
                        }
                        return matchesPreviousOptions;
                    });

                    // Kumpulkan nilai unik untuk opsi saat ini dari varian yang sudah difilter
                    filteredVarians.forEach(varian => {
                        const value = getOptionValue(varian, currentOptionName);
                        if (value && !availableValues.includes(value)) {
                            availableValues.push(value);
                        }
                    });

                    availableValues.sort();

                    // Isi dropdown
                    let optionsHTML = `<option value="">Pilih ${currentOptionName}</option>`;
                    availableValues.forEach(value => {
                        // Cek stok untuk setiap kombinasi yang akan terbentuk jika nilai ini dipilih
                        const tempSelectedOptions = { ...selectedOptions,
                            [currentOptionName]: value
                        };
                        const hasStock = product.varians.some(varian => {
                            let allOptionsMatch = true;
                            for (const name in tempSelectedOptions) {
                                // Hanya cek jika nilai sudah dipilih dan tidak cocok
                                if (tempSelectedOptions[name] && getOptionValue(varian, name) !== tempSelectedOptions[name]) {
                                    allOptionsMatch = false;
                                    break;
                                }
                            }
                            // Pastikan varian yang cocok punya stok
                            return allOptionsMatch && varian.stock > 0;
                        });

                        if (hasStock) {
                            optionsHTML += `<option value="${value}" ${selectedOptions[currentOptionName] === value ? 'selected' : ''}>${value}</option>`;
                        } else {
                            optionsHTML += `<option value="${value}" disabled>${value} (Stok Habis)</option>`;
                        }
                    });

                    selectElement.innerHTML = optionsHTML;
                    // Aktifkan/Nonaktifkan dropdown
                    // Dropdown pertama selalu aktif jika ada opsi
                    if (i === 0) {
                        selectElement.disabled = availableValues.length === 0;
                    } else {
                        // Dropdown berikutnya dinonaktifkan jika opsi sebelumnya belum dipilih atau tidak ada pilihan
                        const prevOptionSelected = selectedOptions[optionNamesOrder[i - 1]];
                        selectElement.disabled = !prevOptionSelected || availableValues.length === 0;
                    }
                    // Jika dropdown ini dinonaktifkan, pastikan nilainya direset
                    if (selectElement.disabled) {
                        selectElement.value = '';
                        selectedOptions[currentOptionName] = '';
                    }
                }
                 updateButtonAndPriceState(product); // Panggil setelah semua dropdown diperbarui
            }


            function updateButtonAndPriceState(product) {
                const modalAddBtn = modalBody.querySelector('#modal-add-btn');
                const priceDisplay = modalBody.querySelector('#modal-display-price');
                let allOptionsSelected = true;
                let currentMatchingVarianFound = null;

                // Cek apakah semua dropdown sudah dipilih
                optionNamesOrder.forEach(optionName => {
                    if (!selectedOptions[optionName]) { // Periksa apakah nilai di selectedOptions kosong
                        allOptionsSelected = false;
                    }
                });

                if (allOptionsSelected && product.varians && Array.isArray(product.varians)) {
                    currentMatchingVarianFound = product.varians.find(varian => {
                        let matchesAllOptions = true;
                        for (const optionName in selectedOptions) {
                            // Perhatikan: getOptionValue harus cocok persis dengan selectedOptions
                            if (getOptionValue(varian, optionName) !== selectedOptions[optionName]) {
                                matchesAllOptions = false;
                                break;
                            }
                            // PERBAIKAN: Jika nilai di selectedOptions kosong, itu tidak cocok
                            if (!selectedOptions[optionName]) {
                                matchesAllOptions = false;
                                break;
                            }
                        }
                        return matchesAllOptions;
                    });
                }

                currentMatchingVarian = currentMatchingVarianFound; // Simpan varian yang cocok

                // Atur status tombol dan harga
                if (currentMatchingVarian && currentMatchingVarian.stock > 0) {
                    modalAddBtn.disabled = false;
                    // Pastikan varianPrice diconvert ke number sebelum diformat
                    const varianPrice = parseFloat(currentMatchingVarian.price) || 0;
                    priceDisplay.textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(varianPrice);
                } else {
                    modalAddBtn.disabled = true;
                    // Jika tidak ada varian yang cocok atau stok habis, tampilkan harga produk utama
                    const fallbackPrice = parseFloat(product.price) || 0;
                    priceDisplay.textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(fallbackPrice);
                }
            }


            // ===================================================================
            // HANDLER UNTUK AKSI (CART & WISHLIST)
            // ===================================================================

            function handleAddToCart(e) {
                e.preventDefault();
                if (isPreview) {
                    showToast('Fitur ini tidak aktif dalam mode pratinjau.', 'error');
                    return;
                }
                // Tambahkan log untuk debug currentMatchingVarian.id
                console.log('Attempting to add to cart. currentMatchingVarian:', currentMatchingVarian);
                console.log('varian_id to send:', currentMatchingVarian ? currentMatchingVarian.id : 'N/A');

                // PERBAIKAN: Periksa juga apakah semua opsi telah dipilih
                // Object.values(selectedOptions).some(value => !value) akan mengecek jika ada value yang kosong
                if (!currentMatchingVarian || optionNamesOrder.some(name => !selectedOptions[name])) {
                    showToast('Silakan pilih semua opsi varian terlebih dahulu.', 'error');
                    return;
                }

                const form = e.target;
                const submitButton = form.querySelector('#modal-add-btn');
                const quantityInput = form.querySelector('#quantity-input');
                const quantity = parseInt(quantityInput.value, 10);

                if (isNaN(quantity) || quantity < 1) {
                    showToast('Jumlah tidak valid.', 'error');
                    return;
                }

                if (currentMatchingVarian.stock < quantity) {
                    showToast(`Stok tidak mencukupi. Tersedia: ${currentMatchingVarian.stock}`, 'error');
                    return;
                }

                // Kirim varian_id dan jumlah ke backend
                const data = {
                    varian_id: currentMatchingVarian.id, // Ambil ID varian yang cocok
                    quantity: quantity
                };

                submitButton.disabled = true;
                submitButton.textContent = 'Menambahkan...';
                axios.post(`/tenant/${subdomain}/cart/add`, data) // Pastikan rute ini menangani `varian_id`
                    .then(response => {
                        if (response.data.success) {
                            showToast(response.data.message, 'success');
                            const cartCountEl = document.querySelector('.header__nav__option #cart-count');
                            if (cartCountEl) {
                                cartCountEl.textContent = response.data.cart_count;
                            }
                            closeModal();
                        } else {
                            throw new Error(response.data.message || 'Gagal menambahkan produk.');
                        }
                    })
                    .catch(error => {
                        // Tambahkan log detail error Axios
                        console.error('AJAX Error:', error.response || error.message);
                        const errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan.';
                        // Jika 422 (Unprocessable Entity) biasanya karena validasi backend
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            let validationMessages = '';
                            for (const key in errors) {
                                validationMessages += errors[key].join(', ') + '; ';
                            }
                            showToast(`Validasi gagal: ${validationMessages}`, 'error');
                        } else {
                            showToast(errorMessage, 'error');
                        }
                    })
                    .finally(() => {
                        if (document.body.contains(submitButton)) {
                            submitButton.disabled = false;
                            submitButton.textContent = 'Tambah ke Keranjang';
                        }
                    });
            }

            function handleToggleWishlist(button) {
                if (isPreview) {
                    showToast('Fitur ini tidak aktif dalam mode pratinjau.', 'error');
                    return;
                }
                if (!csrfToken) {
                    showToast('Terjadi kesalahan. Coba refresh halaman.', 'error');
                    return;
                }
                const productId = button.dataset.productId;
                axios.post(`/tenant/${subdomain}/wishlist/toggle`, {
                        product_id: productId
                    })
                    .then(response => {
                        if (response.data.success) {
                            const allWishlistButtons = document.querySelectorAll(
                                `.toggle-wishlist[data-product-id="${productId}"], #modal-wishlist-btn[data-product-id="${productId}"]`
                            );
                            if (response.data.action === 'added') {
                                showToast('Produk ditambahkan ke Wishlist!', 'success');
                                allWishlistButtons.forEach(btn => btn.classList.add('active'));
                            } else {
                                showToast('Produk dihapus dari Wishlist.', 'success');
                                allWishlistButtons.forEach(btn => btn.classList.remove('active'));
                            }
                            const wishlistCountEl = document.querySelector('.header__nav__option #wishlist-count');
                            if (wishlistCountEl) {
                                wishlistCountEl.textContent = response.data.wishlist_count;
                            }
                        } else {
                            throw new Error(response.data.message || 'Operasi wishlist gagal.');
                        }
                    })
                    .catch(error => {
                        if (error.response?.status === 401) {
                            showToast('Silakan login untuk menggunakan wishlist.', 'error');
                            setTimeout(() => {
                                window.location.href = `/tenant/${subdomain}/customer/login`;
                            }, 1500);
                        } else {
                            const errorMessage = error.response?.data?.message || 'Terjadi kesalahan pada wishlist.';
                            showToast(errorMessage, 'error');
                        }
                    });
            }

            // ===================================================================
            // EVENT LISTENERS AWAL
            // ===================================================================

            document.querySelectorAll('.open-variant-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productData = allProductData[productId];

                    if (productData) {
                        populateAndShowModal(productData);
                    } else {
                        console.error("Data produk tidak ditemukan untuk ID:", productId);
                        showToast("Tidak dapat memuat detail produk.", "error");
                    }
                });
            });

            modal.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay') || e.target.classList.contains(
                        'modal-close')) {
                    closeModal();
                }
            });

            document.querySelectorAll('.toggle-wishlist').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleToggleWishlist(this);
                });
            });
        });
    </script>
@endpush
