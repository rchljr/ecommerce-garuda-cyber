@php
    $isPreview = $isPreview ?? false;
    // Ambil subdomain saat ini sekali saja dari parameter rute agar lebih efisien.
    $currentSubdomain = request()->route('subdomain');
@endphp

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__logo">
                        {{-- PERBAIKAN: Link logo sekarang menunjuk ke halaman utama tenant --}}
                        <a href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}"><img
                                src="{{ asset('img/footer-logo.png') }}" alt=""></a>
                    </div>
                    <p>Pelanggan adalah inti dari model bisnis unik kami, yang mencakup proses desain.</p>
                    <a href="#"><img src="{{ asset('img/payment.png') }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-2 offset-lg-1 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Tentang</h6>
                    <ul>
                        <li><a href="#">Produk Baru</a></li>
                        <li><a href="#">Kualitas Terbaik</a></li>
                        <li><a href="#">Support UMKM</a></li>
                        {{-- PERBAIKAN: Link "Belanja" sekarang menunjuk ke halaman toko tenant --}}
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
                        {{-- PERBAIKAN: Semua link sekarang menggunakan rute tenant yang benar --}}
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
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    <p>Copyright ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        All rights reserved | This template is made with <i class="fa fa-heart-o"
                            aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">PT. Garuda Cyber
                            Indonesia.</a>
                    </p>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                </div>
            </div>
        </div>
    </div>
</footer>