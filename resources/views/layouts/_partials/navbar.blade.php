{{-- <div class="header_section"> --}}
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light header_section sticky-top">
            <a class="navbar-brand" href="{{ route('beranda') }}">
                <img src="images/logoGCI.png" alt="Logo" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beranda') }}#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beranda') }}#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beranda') }}#tema">Tema</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beranda') }}#paket">Biaya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beranda') }}#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beranda') }}#testimoni">Testimoni</a>
                    </li>
                </ul>

                <div class="login_bt">
                    <ul>
                        <li><a href="{{ route('register') }}" class="btn btn-outline-primary">Daftar</a></li>
                        <li><a href="{{ route('login') }}" class="btn btn-primary">Masuk</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    {{-- </div> --}}