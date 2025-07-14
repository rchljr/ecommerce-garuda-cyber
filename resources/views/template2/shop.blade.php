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
            max-width: 550px; border-radius: 8px;
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
                            <img class="img-fluid" src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
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
                                        <span>Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="bottom-area d-flex px-3">
                                <div class="m-auto d-flex">
                                    <a href="#" class="add-to-cart add-cart-button d-flex justify-content-center align-items-center text-center"
                                       data-product-id="{{ $product->id }}"
                                       data-product-name="{{ $product->name }}"
                                       data-product-price="{{ $product->price }}"
                                       data-product-image="{{ asset('storage/' . $product->main_image) }}"
                                       data-product-variants="{{ json_encode($product->variants) }}">
                                        <span><i class="ion-ios-cart"></i></span>
                                    </a>
                                    <a href="{{ !$isPreview ? route('tenant.product.details', ['subdomain' => $currentSubdomain, 'product' => $product->slug]) : '#' }}" class="buy-now d-flex justify-content-center align-items-center mx-1">
                                        <span><i class="ion-ios-flash"></i></span>
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
            <input type="hidden" id="modal-product-id" name="product_id">
            <div id="modal-variant-selection" class="variant-selection"></div>
            <div class="form-group">
                <label for="modal-quantity">Jumlah</label>
                <input type="number" id="modal-quantity" name="quantity" value="1" min="1" class="form-control">
            </div>
            <button type="submit" id="confirm-add-to-cart" class="btn btn-primary py-3 px-5" style="width: 100%; border: none;">Tambah ke Keranjang</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const isPreview = {{ $isPreview ? 'true' : 'false' }};

        const modal = document.getElementById('variant-modal');
        const closeModalBtn = document.querySelector('.variant-modal-close');

        // ## PERBAIKAN DENGAN EVENT DELEGATION ##
        // Memasang satu listener pada dokumen untuk menangkap semua klik.
        document.addEventListener('click', function(e) {
            // Mengecek apakah elemen yang diklik (atau salah satu induknya) adalah tombol '.add-cart-button'
            const button = e.target.closest('.add-cart-button');

            // Jika bukan tombol yang kita cari, hentikan fungsi.
            if (!button) {
                return;
            }

            // Jika tombol ditemukan, jalankan logika modal.
            e.preventDefault();

            if (isPreview) {
                showToast('Fitur ini tidak tersedia dalam mode pratinjau.', 'error');
                return;
            }

            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const productPrice = parseFloat(button.dataset.productPrice);
            const productImage = button.dataset.productImage;
            const variants = JSON.parse(button.dataset.productVariants);

            document.getElementById('modal-product-id').value = productId;
            document.getElementById('modal-product-info').innerHTML = `
                <img src="${productImage}" alt="${productName}" style="width:80px; height:80px; object-fit:cover; border-radius:5px; margin-right: 15px;">
                <div>
                    <h5 style="margin-bottom: 5px;">${productName}</h5>
                    <p class="price" style="font-size: 18px; color: #82ae46; font-weight: 700;">Rp ${productPrice.toLocaleString('id-ID')}</p>
                </div>`;

            const variantContainer = document.getElementById('modal-variant-selection');
            variantContainer.innerHTML = ''; 

            if (variants && variants.length > 0) {
                const sizes = [...new Set(variants.map(v => v.size).filter(v => v))];
                if (sizes.length > 0) {
                    let sizeOptions = sizes.map(s => `<option value="${s}">${s}</option>`).join('');
                    variantContainer.innerHTML += `
                        <div class="form-group"><label for="modal-size">Size</label>
                        <select id="modal-size" name="size" class="form-control" required>${sizeOptions}</select></div>`;
                }
                
                const colors = [...new Set(variants.map(v => v.color).filter(v => v))];
                 if (colors.length > 0) {
                    let colorOptions = colors.map(c => `<option value="${c}">${c}</option>`).join('');
                    variantContainer.innerHTML += `
                        <div class="form-group"><label for="modal-color">Color</label>
                        <select id="modal-color" name="color" class="form-control" required>${colorOptions}</select></div>`;
                }
            }
            modal.style.display = 'block';
        });

        // Logika untuk menutup modal (tidak berubah)
        if (modal) {
            closeModalBtn.onclick = () => modal.style.display = "none";
            window.onclick = (event) => {
                if (event.target == modal) modal.style.display = "none";
            };
        }

        // Logika untuk submit form (tidak berubah)
        const variantForm = document.getElementById('variant-form');
        if (variantForm) {
            variantForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = {
                    product_id: formData.get('product_id'),
                    quantity: formData.get('quantity'),
                    size: formData.get('size'),
                    color: formData.get('color')
                };

                fetch("{{ !$isPreview ? route('tenant.cart.add', ['subdomain' => $currentSubdomain]) : '#' }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement && data.cart_count !== undefined) {
                            cartCountElement.textContent = data.cart_count;
                        }
                        modal.style.display = "none";
                    } else {
                        showToast(data.message || 'Gagal menambahkan produk.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.message) {
                        showToast(error.message, 'error');
                    } else {
                        showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                    }
                });
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-notification');
            if (!toast) return;
            toast.textContent = message;
            toast.className = 'toast-notification';
            toast.classList.add(type, 'show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    });
    </script>
@endpush
