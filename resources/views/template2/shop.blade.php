@extends('template2.layouts.template2')

@section('title', 'Produk Kami')

{{-- CSS Kustom untuk Modal & Notifikasi --}}
@push('styles')
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
        .variant-selection label { font-weight: 600; }
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
    
<div class="hero-wrap hero-bread" style="background-image: url('{{ asset('template2/images/bg_1.jpg') }}');">
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
            <div class="col-md-9 ftco-animate text-center">
                <p class="breadcrumbs">
                    <span class="mr-2">
                        <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Home</a>
                    </span> 
                    <span>Produk</span>
                </p>
                <h1 class="mb-0 bread">Produk Kami</h1>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 mb-5 text-center">
                <ul class="product-category">
                    <li><a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}" class="{{ !request('category') ? 'active' : '' }}">All</a></li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain, 'category' => $category->slug]) : '#' }}" 
                               class="{{ request('category') == $category->slug ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        </li>
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
                            @if($product->is_hot_sale)
                                <span class="status">Hot</span>
                            @endif
                            <div class="overlay"></div>
                        </a>
                        <div class="text py-3 pb-4 px-3 text-center">
                            <h3><a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}">{{ $product->name }}</a></h3>
                            <div class="d-flex">
                                <div class="pricing">
                                    <p class="price">
                                        <span>{{ format_rupiah($product->price) }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="bottom-area d-flex px-3">
                                <div class="m-auto d-flex">
                                    {{-- Tombol ini akan membuka modal varian --}}
                                    <a href="#" class="add-to-cart add-cart-button d-flex justify-content-center align-items-center text-center"
                                       data-product-id="{{ $product->id }}">
                                        <span><i class="ion-ios-cart"></i></span>
                                    </a>
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

{{-- HTML untuk Modal & Notifikasi --}}
<div id="toast-notification" class="toast-notification"></div>

<div id="variant-modal" class="variant-modal">
    <div class="variant-modal-content">
        <span class="variant-modal-close">&times;</span>
        <div id="modal-product-info" class="variant-product-info"></div>
        <form id="variant-form">
            <input type="hidden" id="modal-variant-id" name="varian_id">
            <div id="modal-variant-selection" class="variant-selection"></div>
            <div class="form-group">
                <label for="modal-quantity">Jumlah</label>
                <input type="number" id="modal-quantity" name="quantity" value="1" min="1" class="form-control">
            </div>
            <button type="submit" id="confirm-add-to-cart" class="btn btn-primary py-3 px-5" style="width: 100%; border: none;" disabled>Tambah ke Keranjang</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    {{-- PERBAIKAN: Memuat semua skrip template yang diperlukan SEBELUM skrip custom --}}
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
    <script src="{{ asset('template2/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
    // PERBAIKAN: Membungkus semua logika dalam $(document).ready() untuk memastikan DOM siap
    $(document).ready(function() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const isPreview = {{ $isPreview ? 'true' : 'false' }};
        const currentSubdomain = '{{ $currentSubdomain }}';
        
        const productsData = @json($products->mapWithKeys(function ($product) {
            if (!$product->relationLoaded('varians')) {
                $product->load('varians');
            }
            return [$product->id => $product];
        }));

        const modal = $('#variant-modal');
        const closeModalBtn = $('.variant-modal-close');

        // PERBAIKAN: Menggunakan event delegation jQuery yang lebih andal
        $(document).on('click', '.add-cart-button', function(e) {
            e.preventDefault();
            if (isPreview) {
                showToast('Fitur ini tidak tersedia dalam mode pratinjau.', 'error');
                return;
            }

            const productId = $(this).data('product-id');
            const product = productsData[productId];
            
            if (product) {
                openVariantModal(product);
            }
        });

        function openVariantModal(product) {
            const modalInfo = $('#modal-product-info');
            const variantContainer = $('#modal-variant-selection');

            modalInfo.html(`
                <img src="${product.image_url}" alt="${product.name}" style="width:80px; height:80px; object-fit:cover; border-radius:5px; margin-right: 15px;">
                <div>
                    <h5 style="margin-bottom: 5px;">${product.name}</h5>
                    <p id="modal-price" class="price" style="font-size: 18px; color: #82ae46; font-weight: 700;">${formatRupiah(product.price)}</p>
                    <p id="modal-stock" style="font-size: 14px; color: #888;">Pilih varian untuk melihat stok</p>
                </div>`);

            variantContainer.empty();

            if (!product.varians || product.varians.length === 0) {
                variantContainer.html('<p>Produk ini tidak memiliki varian.</p>');
                modal.css('display', 'block');
                return;
            }

            const optionNames = [...new Set(product.varians.flatMap(v => v.options_data.map(opt => opt.name)))];
            
            optionNames.forEach(name => {
                const selectId = `modal-${name.toLowerCase()}`;
                variantContainer.append(`
                    <div class="form-group">
                        <label for="${selectId}">${name}</label>
                        <select id="${selectId}" data-option-name="${name}" class="form-control" required>
                            <option value="">Pilih ${name}</option>
                        </select>
                    </div>`);
            });
            
            attachVariantListeners(product, optionNames);
            modal.css('display', 'block');
        }

        function attachVariantListeners(product, optionNames) {
            const selects = optionNames.map(name => $(`#modal-${name.toLowerCase()}`));
            const priceEl = $('#modal-price');
            const stockEl = $('#modal-stock');
            const variantIdInput = $('#modal-variant-id');
            const quantityInput = $('#modal-quantity');
            const submitBtn = $('#confirm-add-to-cart');

            const updateState = () => {
                const selectedOptions = {};
                selects.forEach(select => {
                    if (select.val()) {
                        selectedOptions[select.data('option-name')] = select.val();
                    }
                });

                selects.forEach((currentSelect, currentIndex) => {
                    const currentOptionName = currentSelect.data('option-name');
                    
                    let relevantVariants = product.varians;
                    for (let i = 0; i < currentIndex; i++) {
                        const prevSelect = selects[i];
                        const prevOptionName = prevSelect.data('option-name');
                        const prevOptionValue = selectedOptions[prevOptionName];
                        if (prevOptionValue) {
                            relevantVariants = relevantVariants.filter(v => 
                                v.options_data.some(opt => opt.name === prevOptionName && opt.value === prevOptionValue)
                            );
                        }
                    }

                    const availableValues = [...new Set(relevantVariants.map(v => 
                        v.options_data.find(opt => opt.name === currentOptionName)?.value
                    ))].filter(Boolean);

                    const currentValue = currentSelect.val();
                    currentSelect.html(`<option value="">Pilih ${currentOptionName}</option>`);
                    availableValues.forEach(value => {
                        currentSelect.append(new Option(value, value));
                    });
                    
                    if (availableValues.includes(currentValue)) {
                        currentSelect.val(currentValue);
                    }

                    if (currentIndex > 0 && !selects[currentIndex - 1].val()) {
                        currentSelect.prop('disabled', true);
                    } else {
                        currentSelect.prop('disabled', false);
                    }
                });

                const allOptionsSelected = Object.keys(selectedOptions).length === optionNames.length;
                let finalVariant = null;

                if (allOptionsSelected) {
                    finalVariant = product.varians.find(v => 
                        optionNames.every(name => 
                            v.options_data.some(opt => opt.name === name && opt.value === selectedOptions[name])
                        )
                    );
                }

                if (finalVariant && finalVariant.stock > 0) {
                    priceEl.text(formatRupiah(finalVariant.price));
                    stockEl.text(`Stok: ${finalVariant.stock}`);
                    variantIdInput.val(finalVariant.id);
                    quantityInput.attr('max', finalVariant.stock);
                    submitBtn.prop('disabled', false);
                } else {
                    priceEl.text(formatRupiah(product.price));
                    stockEl.text(finalVariant ? 'Stok Habis' : 'Pilih varian untuk melihat stok');
                    variantIdInput.val('');
                    submitBtn.prop('disabled', true);
                }
            };

            selects.forEach(select => select.on('change', updateState));
            updateState();
        }

        closeModalBtn.on('click', () => modal.css('display', "none"));
        $(window).on('click', (event) => {
            if (event.target == modal[0]) modal.css('display', "none");
        });

        $('#variant-form').on('submit', function(e) {
            e.preventDefault();
            const varianId = $('#modal-variant-id').val();
            const quantity = $('#modal-quantity').val();
            
            if (!varianId) {
                showToast('Harap pilih varian yang valid.', 'error');
                return;
            }
            
            const submitBtn = $('#confirm-add-to-cart');
            submitBtn.prop('disabled', true).text('Memproses...');

            axios.post(`{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}`, {
                varian_id: varianId,
                quantity: quantity
            })
            .then(response => {
                const data = response.data;
                if (data.success) {
                    showToast(data.message, 'success');
                    $('#cart-count').text(data.cart_count);
                    modal.css('display', "none");
                } else {
                    throw new Error(data.message || 'Gagal menambahkan produk.');
                }
            })
            .catch(error => {
                showToast(error.response?.data?.message || 'Terjadi kesalahan.', 'error');
            })
            .finally(() => {
                submitBtn.prop('disabled', false).text('Tambah ke Keranjang');
            });
        });

        function showToast(message, type = 'success') {
            const toast = $('#toast-notification');
            if (!toast.length) return;
            toast.text(message).removeClass('success error').addClass(type).addClass('show');
            setTimeout(() => toast.removeClass('show'), 3000);
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }
    });
    </script>
@endpush
