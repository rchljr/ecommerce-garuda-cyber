@extends('template1.layouts.template')

{{-- Gunakan nama produk sebagai judul halaman --}}
@section('title', $product->name ?? 'Detail Produk')

@push('styles')
    {{-- CSS untuk Notifikasi Toast, Varian Aktif, dan Galeri Gambar --}}
    <style>
        .toast-notification {
            position: fixed; bottom: 20px; right: 20px;
            background-color: #333; color: white; padding: 15px 25px;
            border-radius: 8px; z-index: 10001; opacity: 0;
            visibility: hidden; transform: translateY(20px);
            transition: all 0.3s ease-in-out;
        }
        .toast-notification.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .toast-notification.success { background-color: #28a745; }
        .toast-notification.error { background-color: #dc3545; }

        /* Style untuk dropdown varian */
        .variant-options .form-group { margin-bottom: 15px; }
        .variant-options label { font-weight: 600; margin-bottom: 5px; display: block; }
        .variant-options select {
            width: 100%;
            height: 40px;
            padding: 0 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .variant-options select:disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }

        /* Style untuk tombol Add to Cart yang dinonaktifkan */
        .btn-black:disabled {
            background-color: #b0b0b0 !important;
            border-color: #b0b0b0 !important;
            cursor: not-allowed;
        }
        
        /* Style untuk galeri gambar */
        #thumbnail-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        #thumbnail-container img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 4px;
            transition: border-color 0.2s;
        }
        #thumbnail-container img.active,
        #thumbnail-container img:hover {
            border-color: #82ae46; /* Warna utama template */
        }
    </style>
@endpush

@section('content')
    @php
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;

        // Hitung harga termurah dari varian untuk tampilan awal.
        // Jika tidak ada varian, gunakan harga dasar produk.
        $lowestPrice = $product->varians->min('price') ?? $product->price ?? 0;
        $hasDifferentPrices = $product->varians->isNotEmpty() && ($product->varians->min('price') != $product->varians->max('price'));
    @endphp

    <section class="shop-details">
        <div class="product__details__pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__breadcrumb">
                            <a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                            <a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Toko</a>
                            <span>{{ $product->name ?? 'Detail Produk' }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="product__details__pic__container">
                            {{-- Kontainer Gambar Utama --}}
                            <div id="main-image-display" class="mb-3">
                                {{-- Diisi oleh JavaScript --}}
                            </div>
                            {{-- Kontainer Thumbnail --}}
                            <div id="thumbnail-container">
                                {{-- Diisi oleh JavaScript --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="product__details__text">
                            <h4>{{ $product->name ?? 'Nama Produk' }}</h4>
                            {{-- PERUBAHAN: Rating dan jumlah ulasan dinamis --}}
                            <div class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ ($averageRating ?? 0) >= $i ? 'fa-star' : (($averageRating ?? 0) > $i - 1 ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                                <span> - {{ $reviewCount ?? 0 }} Ulasan</span>
                            </div>
                            <h3 id="product-display-price">
                                @if($hasDifferentPrices)
                                    <span style="font-size: 16px; color: #666; font-weight: normal;">Mulai dari </span>
                                @endif
                                {{ format_rupiah($lowestPrice) }}
                            </h3>
                            <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                            <form id="add-to-cart-form"
                                action="{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                                <div class="product__details__option" id="dynamic-variant-options">
                                    @if ($product->varians->isEmpty())
                                        <p class="text-muted">Produk ini tidak memiliki varian.</p>
                                    @endif
                                </div>

                                <div class="product__details__cart__option">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input type="text" name="quantity" value="1">
                                        </div>
                                    </div>
                                    <button type="submit" class="primary-btn" id="add-to-cart-btn"
                                        {{ $product->varians->isEmpty() ? 'disabled' : '' }}>Tambah Keranjang</button>
                                </div>
                            </form>

                            <div class="product__details__btns__option">
                                <a href="#" class="toggle-wishlist" data-product-id="{{ $product->id }}"><i
                                        class="fa fa-heart"></i> Tambah ke Wishlist</a>
                            </div>

                            <div class="product__details__last__option">
                                <h5><span>Garansi & Metode Pembayaran</span></h5>
                                <div class="flex items-center gap-3 my-3">
                                    <img src="{{ asset('images/bca.png') }}" alt="BCA" title="BCA" style="height: 25px; margin: auto;">
                                    <img src="{{ asset('images/bri.png') }}" alt="BRI" title="BRI" style="height: 35px; margin: auto;">
                                    <img src="{{ asset('images/gopay.png') }}" alt="Gopay" title="Gopay" style="height: 25px;">
                                    <img src="{{ asset('images/qris.png') }}" alt="QRIS" title="QRIS" style="height: 25px;">
                                </div>

                                <ul>
                                    <li><span>SKU:</span> <span id="variant-sku">{{ $product->sku ?? '-' }}</span></li>
                                    <li><span>Kategori:</span> {{ $product->subCategory->name ?? 'Uncategorized' }}</li>
                                    <li><span>Tag:</span>
                                        @forelse($product->tags as $tag)
                                            {{ $tag->name }}{{ !$loop->last ? ', ' : '' }}
                                        @empty
                                            -
                                        @endforelse
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Produk --}}
    <section class="ftco-section">
        <div class="container">
            <div class="row">
                {{-- Kolom Gambar Produk --}}
                <div class="col-lg-6 mb-5 ftco-animate">
                    <div id="main-image-display">
                        {{-- Gambar utama akan diisi oleh JavaScript --}}
                    </div>
                    <div id="thumbnail-container">
                        {{-- Thumbnail akan diisi oleh JavaScript --}}
                    </div>
                </div>

                {{-- Kolom Info & Opsi Produk --}}
                <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                    <h3>{{ $product->name }}</h3>
                    <p class="price"><span id="product-display-price">{{ format_rupiah($product->price) }}</span></p>
                    <p>{{ $product->short_description ?? 'Deskripsi singkat produk akan muncul di sini.' }}</p>

                    <form id="add-to-cart-form" method="POST" action="{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" id="selected-variant-id" name="variant_id" value="">

                        {{-- Kontainer untuk dropdown varian dinamis --}}
                        <div class="variant-options mt-4" id="dynamic-variant-options">
                            @if ($product->varians->isEmpty())
                                <p class="text-muted">Produk ini tidak memiliki varian.</p>
                            @endif
                        </div>

                        {{-- Pilihan Kuantitas --}}
                        <div class="row mt-4">
                            <div class="input-group col-md-6 d-flex mb-3">
                                <span class="input-group-btn mr-2">
                                    <button type="button" class="quantity-left-minus btn" data-type="minus" data-field="">
                                        <i class="ion-ios-remove"></i>
                                    </button>
                                </span>
                                <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1" min="1" max="100">
                                <span class="input-group-btn ml-2">
                                    <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="">
                                        <i class="ion-ios-add"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        {{-- Info Stok --}}
                        <div class="col-md-12">
                            <p id="stock_info" style="color: #000;">Stok tersedia: <span id="variant-stock">{{ $product->varians->first()->stock ?? ($product->stock ?? 0) }}</span></p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <p>
                            <button type="submit" class="btn btn-black py-3 px-5" id="add-to-cart-btn" {{ $product->varians->isEmpty() ? 'disabled' : '' }}>
                                Tambah ke Keranjang
                            </button>
                        </p>
                    </form>
                </div>
            </div>
            
            {{-- Deskripsi dan Ulasan --}}
            <div class="row mt-5">
                <div class="col-md-12 nav-link-wrap">
                    <div class="nav nav-pills d-flex text-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link ftco-animate active" id="v-pills-1-tab" data-toggle="pill" href="#v-pills-1" role="tab" aria-controls="v-pills-1" aria-selected="true">Deskripsi</a>
                        <a class="nav-link ftco-animate" id="v-pills-3-tab" data-toggle="pill" href="#v-pills-3" role="tab" aria-controls="v-pills-3" aria-selected="false">Ulasan ({{ $reviewCount ?? 0 }})</a>
                    </div>
                </div>
                <div class="col-md-12 tab-wrap">
                    <div class="tab-content bg-light" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-1" role="tabpanel" aria-labelledby="v-pills-1-tab">
                            <p>{!! $product->description ?? 'Deskripsi lengkap produk akan muncul di sini.' !!}</p>
                        </div>
                        <div class="tab-pane fade" id="v-pills-3" role="tabpanel" aria-labelledby="v-pills-3-tab">
                             <div class="row p-4">
                                <div class="col-md-7">
                                    <h3 class="mb-4">{{ $reviewCount ?? 0 }} Ulasan</h3>
                                    @forelse ($reviews ?? [] as $review)
                                        <div class="review">
                                            <div class="user-img" style="background-image: url({{ asset('template2/images/person_1.jpg') }})"></div>
                                            <div class="desc">
                                                <h4>
                                                    <span class="text-left">{{ $review->name }}</span>
                                                    <span class="float-right">{{ $review->created_at->format('d M Y') }}</span>
                                                </h4>
                                                <p class="star">
                                                    <span>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="ion-ios-star{{ $review->rating >= $i ? '' : '-outline' }}"></i>
                                                        @endfor
                                                    </span>
                                                </p>
                                                <p>{{ $review->content }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p>Belum ada ulasan untuk produk ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Notifikasi Toast --}}
    <div id="toast-notification" class="toast-notification"></div>
@endsection

@push('scripts')
    {{-- Memuat pustaka eksternal --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    {{-- Skrip logika utama yang diadaptasi dari template1 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===================================================================
            // Inisialisasi Variabel Global dan Data
            // ===================================================================
            const productData = @json($product);
            const isPreview = {{ $isPreview ? 'true' : 'false' }};
            const currentSubdomain = '{{ $currentSubdomain }}';
            
            // Elemen DOM
            const dynamicOptionContainer = document.getElementById('dynamic-variant-options');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const selectedVariantInput = document.getElementById('selected-variant-id');
            const stockElement = document.getElementById('variant-stock');
            const priceDisplayElement = document.getElementById('product-display-price');
            const mainImageDisplay = document.getElementById('main-image-display');
            const thumbnailContainer = document.getElementById('thumbnail-container');

            // State
            let selectedOptions = {};
            let optionNamesOrder = [];
            let currentMatchingVarian = null;
            let toastTimeout;

            // ===================================================================
            // Fungsi Bantuan (Helpers)
            // ===================================================================

            function showToast(message, type = 'success') {
                const toastElement = document.getElementById('toast-notification');
                if (!toastElement) return;
                clearTimeout(toastTimeout);
                toastElement.textContent = message;
                toastElement.className = 'toast-notification';
                toastElement.classList.add(type, 'show');
                toastTimeout = setTimeout(() => toastElement.classList.remove('show'), 3000);
            }

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            // Mendapatkan nilai opsi dari struktur data varian
            function getOptionValue(varian, optionName) {
                if (!varian || !varian.options_data || !Array.isArray(varian.options_data)) return null;
                const option = varian.options_data.find(opt => opt.name && opt.name.toLowerCase() === optionName.toLowerCase());
                return option ? option.value : null;
            }
            
            // Memperbarui gambar utama dan thumbnail
            function updateImages(varian) {
                const getFullUrl = (path) => path ? `{{ asset('storage') }}/${path}` : `{{ $product->image_url }}`;
                let primaryImageUrl = getFullUrl(varian?.image_path || productData.main_image);
                
                mainImageDisplay.innerHTML = `<a href="${primaryImageUrl}" class="image-popup"><img src="${primaryImageUrl}" class="img-fluid" alt="${productData.name}"></a>`;

                // Logika untuk thumbnail bisa ditambahkan di sini jika ada galeri gambar
            }

            // ===================================================================
            // Logika Utama Varian
            // ===================================================================

            function initializeVariantSelection() {
                if (!productData.varians || productData.varians.length === 0) {
                    addToCartBtn.disabled = true;
                    updateImages(null);
                    return;
                }

                // Mengumpulkan semua jenis opsi (misal: Ukuran, Warna)
                productData.varians.forEach(varian => {
                    if (varian.options_data && Array.isArray(varian.options_data)) {
                        varian.options_data.forEach(option => {
                            if (option.name && !optionNamesOrder.includes(option.name)) {
                                optionNamesOrder.push(option.name);
                            }
                        });
                    }
                });

                // Membuat HTML untuk dropdown
                let optionsHtml = '';
                optionNamesOrder.forEach(optionName => {
                    optionsHtml += `
                        <div class="form-group">
                            <label for="select-${optionName.toLowerCase()}">${optionName}:</label>
                            <div class="select-wrap">
                                <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                <select id="select-${optionName.toLowerCase()}" class="form-control" data-option-name="${optionName}" disabled>
                                    <option value="">Pilih ${optionName}</option>
                                </select>
                            </div>
                        </div>`;
                });
                dynamicOptionContainer.innerHTML = optionsHtml;

                // Menambahkan event listener ke setiap dropdown
                optionNamesOrder.forEach((optionName, index) => {
                    const selectElement = document.getElementById(`select-${optionName.toLowerCase()}`);
                    if (selectElement) {
                        selectElement.addEventListener('change', () => {
                            selectedOptions[optionName] = selectElement.value;
                            // Reset pilihan dropdown berikutnya
                            for (let i = index + 1; i < optionNamesOrder.length; i++) {
                                selectedOptions[optionNamesOrder[i]] = '';
                                const nextSelect = document.getElementById(`select-${optionNamesOrder[i].toLowerCase()}`);
                                if (nextSelect) nextSelect.value = '';
                            }
                            updateOptionDropdowns(index + 1);
                        });
                    }
                });

                updateOptionDropdowns();
                updateImages(null);
            }

            function updateOptionDropdowns(startIndex = 0) {
                for (let i = startIndex; i < optionNamesOrder.length; i++) {
                    const currentOptionName = optionNamesOrder[i];
                    const selectElement = document.getElementById(`select-${currentOptionName.toLowerCase()}`);
                    if (!selectElement) continue;

                    // Filter varian yang cocok dengan pilihan sebelumnya
                    const filteredVarians = productData.varians.filter(varian => {
                        for (let j = 0; j < i; j++) {
                            const prevOptionName = optionNamesOrder[j];
                            if (selectedOptions[prevOptionName] && getOptionValue(varian, prevOptionName) !== selectedOptions[prevOptionName]) {
                                return false;
                            }
                        }
                        return true;
                    });
                    
                    // Kumpulkan nilai yang tersedia untuk dropdown saat ini
                    const availableValues = [...new Set(filteredVarians.map(v => getOptionValue(v, currentOptionName)).filter(Boolean))].sort();
                    
                    let optionsHTML = `<option value="">Pilih ${currentOptionName}</option>`;
                    availableValues.forEach(value => {
                        // Cek apakah ada varian dengan nilai ini yang stoknya > 0
                        const hasStock = filteredVarians.some(varian => getOptionValue(varian, currentOptionName) === value && varian.stock > 0);
                        optionsHTML += `<option value="${value}" ${hasStock ? '' : 'disabled'}>${value} ${hasStock ? '' : '(Habis)'}</option>`;
                    });
                    selectElement.innerHTML = optionsHTML;

                    // Aktifkan dropdown jika dropdown sebelumnya sudah dipilih
                    const prevOptionSelected = (i === 0) || (selectedOptions[optionNamesOrder[i - 1]]);
                    selectElement.disabled = !prevOptionSelected || availableValues.length === 0;

                    if (selectElement.disabled) {
                        selectedOptions[currentOptionName] = '';
                    }
                }
                updateState();
            }

            function updateState() {
                const allOptionsSelected = optionNamesOrder.length === 0 || optionNamesOrder.every(name => selectedOptions[name]);
                
                currentMatchingVarian = null;
                if (allOptionsSelected && productData.varians.length > 0) {
                    currentMatchingVarian = productData.varians.find(varian => {
                        return optionNamesOrder.every(name => getOptionValue(varian, name) === selectedOptions[name]);
                    });
                }

                if (currentMatchingVarian && currentMatchingVarian.stock > 0) {
                    addToCartBtn.disabled = false;
                    priceDisplayElement.textContent = formatRupiah(currentMatchingVarian.price);
                    stockElement.textContent = currentMatchingVarian.stock;
                    selectedVariantInput.value = currentMatchingVarian.id;
                    updateImages(currentMatchingVarian);
                } else {
                    addToCartBtn.disabled = true;
                    priceDisplayElement.textContent = formatRupiah(productData.price);
                    stockElement.textContent = productData.varians.first()?.stock ?? (productData.stock ?? 0);
                    selectedVariantInput.value = '';
                    if (!allOptionsSelected) updateImages(null);
                }
            }

            // ===================================================================
            // Handler untuk Aksi (Form Submit, Kuantitas)
            // ===================================================================

            document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
                e.preventDefault();
                if (isPreview) return;

                const form = this;
                const submitButton = form.querySelector('#add-to-cart-btn');
                const quantity = parseInt(form.querySelector('input[name="quantity"]').value, 10);

                if (!currentMatchingVarian) {
                    showToast('Silakan pilih semua opsi varian.', 'error');
                    return;
                }
                if (isNaN(quantity) || quantity < 1) {
                    showToast('Jumlah tidak valid.', 'error');
                    return;
                }
                if (currentMatchingVarian.stock < quantity) {
                    showToast(`Stok tidak mencukupi. Tersedia: ${currentMatchingVarian.stock}`, 'error');
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Menambahkan...';

                axios.post(form.action, {
                    product_id: productData.id,
                    variant_id: currentMatchingVarian.id,
                    quantity: quantity
                })
                .then(res => {
                    if (res.data.success) {
                        showToast(res.data.message || 'Produk berhasil ditambahkan!', 'success');
                        if (res.data.cart_count !== undefined) {
                            const cartCountElement = document.querySelector('.cart-icon span');
                            if(cartCountElement) cartCountElement.textContent = res.data.cart_count;
                        }
                    } else { throw new Error(res.data.message); }
                })
                .catch(err => showToast(err.response?.data?.message || 'Gagal menambahkan produk.', 'error'))
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Tambah ke Keranjang';
                });
            });
            
            // Logika untuk tombol +/- kuantitas
            $('.quantity-left-minus').on('click', function(){
                var $input = $(this).closest('.input-group').find('input');
                var count = parseInt($input.val(), 10) - 1;
                count = count < 1 ? 1 : count;
                $input.val(count);
                $input.trigger('change');
            });
            $('.quantity-right-plus').on('click', function(){
                var $input = $(this).closest('.input-group').find('input');
                $input.val(parseInt($input.val(), 10) + 1);
                $input.trigger('change');
            });

            // Panggil inisialisasi utama
            initializeVariantSelection();
        });
    </script>
@endpush