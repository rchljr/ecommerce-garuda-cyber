@extends('template2.layouts.template2')

@section('title', 'Produk Kami')

{{-- CSS Kustom untuk Modal & Notifikasi --}}
@push('styles')
    <style>
        /* [CSS tidak berubah] */
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
        .variant-modal {
            display: none; position: fixed; z-index: 10000;
            left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0, 0, 0, 0.5);
            -webkit-animation: fadeIn 0.4s; animation: fadeIn 0.4s;
        }
        .variant-modal-content {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%); background-color: #fefefe;
            padding: 30px; border: 1px solid #888; width: 90%;
            max-width: 450px; border-radius: 8px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            -webkit-animation: slideIn 0.4s; animation: slideIn 0.4s;
        }
        .variant-modal-close {
            color: #aaa; float: right; font-size: 28px;
            font-weight: bold; line-height: 1; cursor: pointer;
        }
        .variant-product-info {
            display: flex; align-items: center; border-bottom: 1px solid #eee;
            padding-bottom: 20px; margin-bottom: 20px;
        }
        .variant-selection .form-group { margin-bottom: 15px; }
        .variant-selection label { font-weight: 600; margin-bottom: 5px; display: block; }
        .variant-selection select, .variant-selection input { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd; }
        .variant-selection select:disabled { background-color: #f0f0f0; cursor: not-allowed; }
        #confirm-add-to-cart:disabled { background-color: #ccc; border-color: #ccc; cursor: not-allowed; }
        @-webkit-keyframes slideIn { from {top: -300px; opacity: 0} to {top: 50%; opacity: 1} }
        @keyframes slideIn { from {top: -300px; opacity: 0} to {top: 50%; opacity: 1} }
        @-webkit-keyframes fadeIn { from {opacity: 0} to {opacity: 1} }
        @keyframes fadeIn { from {opacity: 0} to {opacity: 1} }
    </style>
@endpush


@section('content')
@php
    $isPreview = $isPreview ?? false;
    $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
@endphp

{{-- Bagian Hero --}}
<div class="hero-wrap hero-bread" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
            <div class="col-md-9 ftco-animate text-center">
                <p class="breadcrumbs"><span class="mr-2"><a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a></span> <span>Produk</span></p>
                <h1 class="mb-0 bread">Produk Kami</h1>
            </div>
        </div>
    </div>
</div>

{{-- Bagian Produk --}}
<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 mb-5 text-center">
                <ul class="product-category">
                    <li><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}" class="{{ !request('category') ? 'active' : '' }}">All</a></li>
                    @foreach ($categories as $category)
                        <li><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain, 'category' => $category->slug]) : '#' }}" class="{{ request('category') == $category->slug ? 'active' : '' }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row">
            @forelse ($products as $product)
                <div class="col-md-6 col-lg-3 ftco-animate">
                    <div class="product">
                        <a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}" class="img-prod">
                            <img class="img-fluid" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            @if($product->is_hot_sale) <span class="status">Hot</span> @endif
                            <div class="overlay"></div>
                        </a>
                        <div class="text py-3 pb-4 px-3 text-center">
                            <h3><a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}">{{ $product->name }}</a></h3>
                            <div class="d-flex"><div class="pricing"><p class="price"><span>{{ format_rupiah($product->price) }}</span></p></div></div>
                            <div class="bottom-area d-flex px-3">
                                <div class="m-auto d-flex">
                                    {{-- PERBAIKAN: Menggunakan button dan class yang konsisten --}}
                                    <button type="button" class="add-to-cart add-cart-button d-flex justify-content-center align-items-center mx-1" data-product-id="{{ $product->id }}">
                                        <span><i class="ion-ios-cart"></i></span>
                                    </button>
                                    <a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}" class="buy-now d-flex justify-content-center align-items-center mx-1">
                                        <span><i class="ion-ios-eye"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <h3>Oops!</h3>
                    <p>Produk dalam kategori ini belum tersedia.</p>
                </div>
            @endforelse
        </div>
        <div class="row mt-5">
            <div class="col text-center">
                <div class="block-27">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PERBAIKAN: Struktur HTML Modal dikembalikan ke versi yang benar untuk template2 --}}
<div id="toast-notification" class="toast-notification"></div>
<div id="variant-modal" class="variant-modal">
    <div class="variant-modal-content">
        <span class="variant-modal-close">&times;</span>
        <div id="modal-product-info" class="variant-product-info">
            {{-- Info produk akan diisi oleh JS --}}
        </div>
        <form id="variant-form" novalidate>
            <input type="hidden" id="modal-variant-id" name="varian_id">
            <div id="modal-variant-selection" class="variant-selection">
                {{-- Dropdown varian akan diisi oleh JS --}}
            </div>
            <div class="form-group">
                <label for="modal-quantity">Jumlah</label>
                <input type="number" id="modal-quantity" name="quantity" value="1" min="1" class="form-control">
            </div>
            <button type="submit" id="confirm-add-to-cart" class="btn btn-primary py-3 px-5" style="width: 100%; border: none;" disabled>Pilih Varian</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Memuat semua skrip template yang diperlukan SEBELUM skrip kustom --}}
    <script src="{{ asset('template2/js/jquery.min.js') }}"></script>
    <script src="{{ asset('template2/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('template2/js/popper.min.js') }}"></script>
    <script src="{{ asset('template2/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template2/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset('template2/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('template2/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('template2/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template2/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('template2/js/aos.js') }}"></script>
    <script src="{{ asset('template2/js/jquery.animateNumber.min.js') }}"></script>
    <script src="{{ asset('template2/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('template2/js/scrollax.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
    $(document).ready(function() {
        const isPreview = {{ $isPreview ? 'true' : 'false' }};
        const currentSubdomain = '{{ $currentSubdomain }}';

        const productsData = @json($products->keyBy('id')->map(function ($product) {
            if (!$product->relationLoaded('varians')) {
                $product->load('varians');
            }
            $product->processed_varians = $product->varians->map(function ($varian) {
                $optionsMap = [];
                if (is_array($varian->options_data)) {
                    foreach ($varian->options_data as $option) {
                        if (isset($option['name']) && isset($option['value'])) {
                           $optionsMap[$option['name']] = $option['value'];
                        }
                    }
                }
                $varian->options_map = $optionsMap;
                return $varian;
            });
            return $product;
        }));

        const modal = $('#variant-modal');
        let currentProduct = null; 

        $(document).on('click', '.add-cart-button', function(e) {
            e.preventDefault();
            if (isPreview) {
                showToast('Fitur ini tidak tersedia dalam mode pratinjau.', 'error');
                return;
            }
            const productId = $(this).data('product-id');
            currentProduct = productsData[productId];
            if (currentProduct) {
                openVariantModal(currentProduct);
            }
        });

        function openVariantModal(product) {
            // ... (Fungsi ini tidak perlu diubah) ...
            const modalInfo = $('#modal-product-info');
            const variantContainer = $('#modal-variant-selection');

            $('#variant-form')[0].reset();
            $('#confirm-add-to-cart').prop('disabled', true).text('Pilih Varian');

            modalInfo.html(`
                <img src="${product.image_url}" alt="${product.name}" style="width:80px; height:80px; object-fit:cover; border-radius:5px; margin-right: 15px;">
                <div>
                    <h5 id="modal-product-name" style="margin-bottom: 5px;">${product.name}</h5>
                    <p id="modal-price" class="price" style="font-size: 18px; color: #82ae46; font-weight: 700;">${formatRupiah(product.price)}</p>
                    <p id="modal-stock" style="font-size: 14px; color: #888;">Pilih varian untuk melihat stok</p>
                </div>`);

            variantContainer.empty();

            if (!product.processed_varians || product.processed_varians.length === 0) {
                variantContainer.html('<p>Produk ini tidak memiliki varian.</p>');
                modal.css('display', 'block');
                return;
            }

            const optionNames = [...new Set(product.processed_varians.flatMap(v => Object.keys(v.options_map)))];
            
            optionNames.forEach((name, index) => {
                const selectId = `variant-select-${index}`;
                variantContainer.append(`
                    <div class="form-group">
                        <label for="${selectId}">${name}</label>
                        <select id="${selectId}" class="form-control variant-select" data-option-name="${name}">
                            <option value="">Pilih ${name}</option>
                        </select>
                    </div>`);
            });
            
            attachVariantListeners(product, optionNames);
            modal.css('display', 'block');
        }
        
        function attachVariantListeners(product, optionNames) {
            // ... (Fungsi ini tidak perlu diubah) ...
            const selects = $('.variant-select');
            const priceEl = $('#modal-price');
            const stockEl = $('#modal-stock');
            const variantIdInput = $('#modal-variant-id');
            const quantityInput = $('#modal-quantity');
            const submitBtn = $('#confirm-add-to-cart');

            const updateState = () => {
                let selectedOptions = {};
                selects.each(function() {
                    const select = $(this);
                    if (select.val()) {
                        selectedOptions[select.data('option-name')] = select.val();
                    }
                });
                
                selects.each(function(currentIndex) {
                    const currentSelect = $(this);
                    let relevantVariants = product.processed_varians;
                    for (let i = 0; i < currentIndex; i++) {
                        const prevSelect = $(selects[i]);
                        const prevOptionName = prevSelect.data('option-name');
                        const prevValue = selectedOptions[prevOptionName];
                        if (prevValue) {
                            relevantVariants = relevantVariants.filter(v => v.options_map[prevOptionName] === prevValue);
                        }
                    }
                    const currentOptionName = currentSelect.data('option-name');
                    const availableValues = [...new Set(relevantVariants.map(v => v.options_map[currentOptionName]))].filter(Boolean);
                    if (currentIndex > 0) {
                         currentSelect.prop('disabled', !$(selects[currentIndex - 1]).val());
                         if(!$(selects[currentIndex - 1]).val()){
                             currentSelect.val("");
                         }
                    }
                    if (currentIndex === 0 || $(selects[currentIndex - 1]).val()) {
                        const currentVal = currentSelect.val();
                        currentSelect.html(`<option value="">Pilih ${currentOptionName}</option>`);
                        availableValues.forEach(value => {
                            currentSelect.append(new Option(value, value));
                        });
                        currentSelect.val(currentVal);
                    }
                });

                const allOptionsSelected = Object.keys(selectedOptions).length === optionNames.length && Object.values(selectedOptions).every(v => v !== "");
                let finalVariant = null;
                if (allOptionsSelected) {
                    finalVariant = product.processed_varians.find(v => 
                        optionNames.every(name => v.options_map[name] === selectedOptions[name])
                    );
                }

                if (finalVariant) {
                    priceEl.text(formatRupiah(finalVariant.price));
                    variantIdInput.val(finalVariant.id);
                    quantityInput.attr('max', finalVariant.stock);
                    if (finalVariant.stock > 0) {
                        stockEl.text(`Stok: ${finalVariant.stock}`).css('color', '#28a745');
                        submitBtn.prop('disabled', false).text('Tambah ke Keranjang');
                    } else {
                        stockEl.text('Stok Habis').css('color', '#dc3545');
                        submitBtn.prop('disabled', true).text('Stok Habis');
                    }
                } else {
                    priceEl.text(formatRupiah(product.price));
                    stockEl.text('Pilih varian untuk melihat stok').css('color', '#888');
                    variantIdInput.val('');
                    submitBtn.prop('disabled', true).text('Pilih Varian');
                }
            };
            selects.on('change', updateState);
            updateState();
        }

        $(document).on('click', '.variant-modal-close', () => modal.css('display', "none"));
        $(window).on('click', (event) => {
            if ($(event.target).is(modal)) {
                modal.css('display', "none");
            }
        });
        
        $('#variant-form').on('submit', function(e) {
            e.preventDefault();
            
            // PERBAIKAN DI SINI: Gunakan selector ID (#) agar lebih spesifik dan andal
            const variantId = $('#modal-variant-id').val(); 
            
            const quantity = $('#modal-quantity').val();
            
            if (!currentProduct) {
                showToast('Terjadi kesalahan: Produk tidak ditemukan.', 'error');
                return;
            }
            if (!variantId) {
                showToast('Harap pilih varian yang valid.', 'error');
                return;
            }
            
            const submitBtn = $('#confirm-add-to-cart');
            submitBtn.prop('disabled', true).text('Memproses...');

            const postData = {
                product_id: currentProduct.id,
                variant_id: variantId,
                quantity: quantity
            };

            axios.post(`{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}`, postData)
            .then(response => {
                if (response.data.success) {
                    showToast(response.data.message, 'success');
                    $('#cart-count').text(response.data.cart_count);
                    modal.css('display', "none");
                } else { throw new Error(response.data.message); }
            })
            .catch(error => showToast(error.response?.data?.message || 'Terjadi kesalahan.', 'error'))
            .finally(() => {
                submitBtn.prop('disabled', false).text('Tambah ke Keranjang');
                currentProduct = null; 
            });
        });

        function showToast(message, type = 'success') {
            $('#toast-notification').text(message).removeClass('success error').addClass(type).addClass('show');
            setTimeout(() => $('#toast-notification').removeClass('show'), 3000);
        }
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }
    });
    </script>
@endpush