{{-- resources/views/template1/sections/banner.blade.php --}}
{{-- Variabel $content berisi array data banner section dari CMS, sekarang dengan image_1, image_2, image_3 --}}

<section class="banner spad">
    <div class="container">
        <div class="row">
            {{-- Banner Item 1 --}}
            <div class="col-lg-7 offset-lg-4">
                <div class="banner__item">
                    <div class="banner__item__pic">
                        {{-- Menggunakan asset() untuk gambar yang diunggah. Tambahkan default jika gambar tidak ada. --}}
                        <img src="{{ asset('storage/' . ($content['image_1'] ?? 'template1/img/banner/banner-1.jpg')) }}" alt="{{ $content['title_1'] ?? 'Banner 1' }}">
                    </div>
                    <div class="banner__item__text">
                        {{-- Menampilkan judul dari CMS --}}
                        <h2>{{ $content['title_1'] ?? 'Default Banner Title 1' }}</h2>
                        {{-- Menampilkan tombol dan URL dari CMS --}}
                        <a href="{{ $content['url_1'] ?? '#' }}">{{ $content['button_text_1'] ?? 'Shop now' }}</a>
                    </div>
                </div>
            </div>

            {{-- Banner Item 2 --}}
            <div class="col-lg-5">
                <div class="banner__item banner__item--middle">
                    <div class="banner__item__pic">
                        <img src="{{ asset('storage/' . ($content['image_2'] ?? 'template1/img/banner/banner-2.jpg')) }}" alt="{{ $content['title_2'] ?? 'Banner 2' }}">
                    </div>
                    <div class="banner__item__text">
                        <h2>{{ $content['title_2'] ?? 'Default Banner Title 2' }}</h2>
                        <a href="{{ $content['url_2'] ?? '#' }}">{{ $content['button_text_2'] ?? 'Shop now' }}</a>
                    </div>
                </div>
            </div>

            {{-- Banner Item 3 --}}
            <div class="col-lg-7">
                <div class="banner__item banner__item--last">
                    <div class="banner__item__pic">
                        <img src="{{ asset('storage/' . ($content['image_3'] ?? 'template1/img/banner/banner-3.jpg')) }}" alt="{{ $content['title_3'] ?? 'Banner 3' }}">
                    </div>
                    <div class="banner__item__text">
                        <h2>{{ $content['title_3'] ?? 'Default Banner Title 3' }}</h2>
                        <a href="{{ $content['url_3'] ?? '#' }}">{{ $content['button_text_3'] ?? 'Shop now' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
