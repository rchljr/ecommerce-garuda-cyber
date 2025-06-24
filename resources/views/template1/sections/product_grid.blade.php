{{-- resources/views/template1/sections/product_grid.blade.php --}}
{{-- Variabel $content berisi array data konfigurasi grid produk dari CMS --}}
{{-- Variabel $productsForGrid akan berisi daftar produk yang sudah diambil --}}

<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                @if(isset($content['heading']) && $content['heading'])
                    <div class="section-title">
                        <span>{{ $content['heading'] }}</span>
                        <h2>{{ $content['heading'] }}</h2> {{-- Atau judul yang berbeda --}}
                    </div>
                @else
                    <div class="section-title">
                        <span>Produk Kami</span>
                        <h2>Pilih Sesuai Gaya Anda</h2>
                    </div>
                @endif
                
                {{-- Filter controls (opsional, bisa dinamis atau statis) --}}
                <ul class="filter__controls">
                    <li class="active" data-filter="*">All Products</li>
                    <li data-filter=".new-arrivals">New Arrivals</li>
                    <li data-filter=".hot-sales">Hot Sales</li>
                </ul>
            </div>
        </div>
        <div class="row product__filter">
            @forelse ($productsForGrid ?? [] as $product)
                <div class="col-lg-3 col-md-6 col-sm-6 mix new-arrivals"> {{-- Anda mungkin perlu kelas mix yang dinamis --}}
                    <div class="product__item">
                        <div class="product__item__pic set-bg" data-setbg="{{ asset('storage/' . ($product->image ?? 'placeholder.jpg')) }}">
                            @if($product->is_new ?? false) {{-- Asumsi ada properti is_new di model/data --}}
                                <span class="label">New</span>
                            @endif
                            <ul class="product__hover">
                                <li><a href="#"><img src="{{ asset('template1/img/icon/heart.png') }}" alt=""></a></li>
                                <li><a href="#"><img src="{{ asset('template1/img/icon/compare.png') }}" alt=""> <span>Compare</span></a></li>
                                <li><a href="{{ url('/shop-details/' . $product->slug) }}"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a></li>
                            </ul>
                        </div>
                        <div class="product__item__text">
                            <h6>{{ $product->name }}</h6>
                            <a href="#" class="add-cart">+ Add To Cart</a>
                            <div class="rating">
                                {{-- Rating statis, bisa dinamis nanti --}}
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                            </div>
                            <h5>${{ number_format($product->price, 2) }}</h5>
                            {{-- Warna produk, bisa dinamis nanti --}}
                            <div class="product__color__select">
                                <label for="pc-1">
                                    <input type="radio" id="pc-1">
                                </label>
                                <label class="active black" for="pc-2">
                                    <input type="radio" id="pc-2">
                                </label>
                                <label class="grey" for="pc-3">
                                    <input type="radio" id="pc-3">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-lg-12 text-center py-5">
                    <p class="text-gray-500">Tidak ada produk ditemukan untuk seksi ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
