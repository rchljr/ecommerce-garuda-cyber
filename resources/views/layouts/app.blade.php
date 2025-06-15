<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts._partials.head')
</head>

<body class="body-bg-mitra">
    <div class="layout-container-mitra">
        <aside class="sidebar-mitra">
            @include('layouts._partials.sidebar-mitra')
        </aside>

        <main class="main-content-mitra">
            @include('layouts._partials.topbar')

            <div class="profile-mitra">
                <img src="{{ asset('images/user.svg') }}" alt="Profile" class="profile-img-mitra">
                <span class="profile-name-mitra">Hi, Mitra!</span>
            </div>

            @yield('content')

            <footer class="footer-mitra">
                © 2025 PT Garuda Cyber Indonesia
            </footer>
        </main>
    </div>
</body>

</html>