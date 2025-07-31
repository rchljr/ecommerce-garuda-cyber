@php
    $isPreview = $isPreview ?? false;
    // Ambil subdomain saat ini sekali saja dari parameter rute agar lebih efisien.
    $currentSubdomain = request()->route('subdomain');
    $currentShop = null;
    $customTema = null;
    // PERBAIKAN: Logika untuk logo dan deskripsi dipindahkan ke sini agar lebih bersih
    // Asumsikan variabel $customTema tersedia dari View Composer atau Controller
    $logoPath = optional($customTema)->shop_logo;
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('images/gci.png');
    $shopName = optional($customTema)->shop_name ?? 'Nama Toko';
    $shopDescription =
        optional($customTema)->shop_description ??
        'Pelanggan adalah inti dari model bisnis unik kami, yang mencakup proses desain.';
@endphp

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">
                            {{-- PERBAIKAN: Menggunakan variabel logo dan nama toko yang sudah disiapkan --}}
                            <img src="{{ $logoUrl }}" alt="{{ $shopName }}"
                                style="max-height: 65px; background: white; padding: 10px; border-radius: 100px;">
                        </a>
                    </div>
                    {{-- PERBAIKAN: Menggunakan deskripsi toko yang dinamis --}}
                    <p>{{ $shopDescription }}</p>

                    @if ($contact)
                        <div class="footer__social mt-3">
                            @if ($contact->facebook_url)
                                <a href="{{ $contact->facebook_url }}" target="_blank"><i
                                        class="fa fa-facebook"></i></a>
                            @endif
                            @if ($contact->twitter_url)
                                <a href="{{ $contact->twitter_url }}" target="_blank"><i class="fa fa-twitter"></i></a>
                            @endif
                            @if ($contact->pinterest_url)
                                <a href="{{ $contact->pinterest_url }}" target="_blank"><i
                                        class="fa fa-pinterest"></i></a>
                            @endif
                            @if ($contact->instagram_url)
                                <a href="{{ $contact->instagram_url }}" target="_blank"><i
                                        class="fa fa-instagram"></i></a>
                            @endif
                        </div>
                    @endif


                    <div class="footer__payment" style="margin-top: 20px;">
                        <h6 style="margin-bottom: 15px; color: #b7b7b7; font-size: 15px;">Metode Pembayaran</h6>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="payment-logo-bg"><img src="{{ asset('images/bca.png') }}" alt="BCA"></div>
                            <div class="payment-logo-bg"><img
                                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/BRI_2020.svg/2560px-BRI_2020.svg.png"
                                    alt="BRI"></div>
                            <div class="payment-logo-bg"><img src="{{ asset('images/gopay.png') }}" alt="Gopay">
                            </div>
                            <div class="payment-logo-bg"><img src="{{ asset('images/qris.png') }}" alt="QRIS">
                            </div>
                        </div>
                    </div>

                    <div class="footer__shipping" style="margin-top: 20px;">
                        <h6 style="margin-bottom: 15px; color: #b7b7b7; font-size: 15px;">Pilihan Pengiriman</h6>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="payment-logo-bg"><img src="{{ asset('images/jne.png') }}" alt="JNE"></div>
                            <div class="payment-logo-bg"><img src="{{ asset('images/jnt.png') }}" alt="J&T"></div>
                            <div class="payment-logo-bg"><img src="{{ asset('images/sicepat.png') }}" alt="SiCepat">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-2 offset-lg-1 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Tentang</h6>
                    <ul>
                        <li><a href="#">Produk Baru</a></li>
                        <li><a href="#">Kualitas Terbaik</a></li>
                        <li><a href="#">Support UMKM</a></li>
                        <li><a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Belanja</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Belanja</h6>
                    <ul>
                        <li><a
                                href="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}">Kontak
                                Kami</a></li>
                        <li><a
                                href="{{ !$isPreview ? route('tenant.shop', ['subdomain' => $currentSubdomain]) : '#' }}">Produk</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 offset-lg-1 col-md-6 col-sm-6">
                <div class="footer__widget">
                    <h6>Kabar Terkini</h6>
                    <div class="footer__newslatter">
                        <p>Jadilah yang pertama tahu tentang kedatangan produk baru, lookbook, diskon & promo menarik!
                        </p>
                        <form action="#">
                            <input type="text" placeholder="Your email">
                            <button type="submit"><span class="icon_mail_alt"></span></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="footer__copyright__text">
                    <p>Copyright ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        All rights reserved | Ditenagai oleh
                        <a href="{{ route('tim.developer') }}" target="_blank">Tim E-Commerce Garuda</a>
                        by <a href="https://pcr.ac.id/" target="_blank">Politeknik Caltex Riau</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Menambahkan style untuk background logo pembayaran & pengiriman --}}
<style>
    .payment-logo-bg {
        background-color: white;
        padding: 5px 8px;
        border-radius: 4px;
        margin-right: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payment-logo-bg img {
        height: 20px;
        max-width: 50px;
        object-fit: contain;
    }
</style>
