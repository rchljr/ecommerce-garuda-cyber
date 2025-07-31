<section class="ftco-section ftco-no-pt ftco-no-pb py-5 bg-light">
    <div class="container py-4">
        <div class="row d-flex justify-content-center py-5">
            {{-- <div class="col-md-6">
                <h2 style="font-size: 22px;" class="mb-0">Subcribe to our Newsletter</h2>
                <span>Get e-mail updates about our latest shops and special offers</span>
            </div> --}}
            {{-- <div class="col-md-6 d-flex align-items-center">
                <form action="#" class="subscribe-form">
                    <div class="form-group d-flex">
                        <input type="text" class="form-control" placeholder="Enter email address">
                        <input type="submit" value="Subscribe" class="submit px-3">
                    </div>
                </form>
            </div> --}}
        </div>
    </div>
</section>
<footer class="ftco-footer ftco-section">
    <div class="container">
        <div class="row">
            <div class="mouse">
                <a href="#" class="mouse-icon">
                    <div class="mouse-wheel"><span class="ion-ios-arrow-up"></span></div>
                </a>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Selalu Sedia Membantu</h2>
                    <p>{{ $customTema->shop_description ?? 'Deskripsi toko belum tersedia.' }}</p>
                    <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                        @if ($contact?->twitter_url)
                            <li class="ftco-animate"><a href="{{ $contact->twitter_url }}" target="_blank"><span
                                        class="icon-twitter"></span></a></li>
                        @endif
                        @if ($contact?->facebook_url)
                            <li class="ftco-animate"><a href="{{ $contact->facebook_url }}" target="_blank"><span
                                        class="icon-facebook"></span></a></li>
                        @endif
                        @if ($contact?->instagram_url)
                            <li class="ftco-animate"><a href="{{ $contact->instagram_url }}" target="_blank"><span
                                        class="icon-instagram"></span></a></li>
                        @endif
                    </ul>

                </div>
            </div>
            <div class="col-md">
                <div class="ftco-footer-widget mb-4 ml-md-5">
                    <h2 class="ftco-heading-2">Menu</h2>
                    <ul class="list-unstyled">
                        <li><a href="#" class="py-2 d-block">Beranda</a></li>
                        <li><a href="#" class="py-2 d-block">Toko</a></li>
                        <li><a href="#" class="py-2 d-block">Keranjang</a></li>
                        <li><a href="#" class="py-2 d-block">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                {{-- <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Pembayaran</h2>
                    <div class="d-flex">
                        <ul class="list-unstyled mr-l-5 pr-l-3 mr-4">
                            <li><a href="#" class="py-2 d-block">Shipping Information</a></li>
                            <li><a href="#" class="py-2 d-block">Returns &amp; Exchange</a></li>
                            <li><a href="#" class="py-2 d-block">Terms &amp; Conditions</a></li>
                            <li><a href="#" class="py-2 d-block">Privacy Policy</a></li>
                        </ul>
                    </div> 
                </div> --}}
            </div>
            <div class="col-md">
                <div class="col-md">
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2">Informasi Toko</h2>
                        <ul class="list-unstyled">
                            @if ($contact?->phone)
                                <li class="py-1"><span class="icon-phone"></span> {{ $contact->phone }}</li>
                            @endif
                            @if ($contact?->email)
                                <li class="py-1"><span class="icon-envelope"></span> {{ $contact->email }}</li>
                            @endif
                            @if ($contact?->address)
                                <li class="py-1"><span class="icon-map-marker"></span> {{ $contact->address }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
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
<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
        <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
        <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4"
            stroke-miterlimit="10" stroke="#F96D00" />
    </svg></div>
