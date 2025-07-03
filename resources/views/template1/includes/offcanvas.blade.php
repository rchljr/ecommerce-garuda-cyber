<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
        {{-- Link ini akan selalu tampil --}}
            <a href="#">FAQs</a>
            {{-- Tampilkan link ini jika pengguna adalah tamu (belum login) --}}
            @guest
                <a href="{{ route('customer.login.form') }}">Login</a>
                <a href="{{ route('customer.register.form') }}">Daftar</a>
            @endguest

            {{-- Tampilkan menu ini jika pengguna sudah login --}}
            @auth
                <div class="header__top__dropdown">
                    <a href="#"><i class="fa fa-user"></i> Hi, {{ strtok(Auth::user()->name, ' ') }}</a>
                    <span class="arrow_carrot-down"></span>
                    <ul>
                        <li>
                            <a href="{{ route('customer.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form-offcanvas').submit();">
                                Log Out
                            </a>
                            <form id="logout-form-offcanvas" action="{{ route('customer.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="#" class="search-switch"><img src="{{ asset('template1/img/icon/search.png') }}" alt=""></a>
        <a href="#"><img src="{{ asset('tempalte1/img/icon/heart.png') }}" alt=""></a>
        <a href="#"><img src="{{ asset('template1/img/icon/cart.png') }}" alt=""> <span>0</span></a>
        <div class="price">$0.00</div>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__text">
        <p>Free shipping, 30-day return or refund guarantee.</p>
    </div>
</div>