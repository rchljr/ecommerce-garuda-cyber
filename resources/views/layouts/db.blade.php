<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts._partials.head')
</head>

<body>
    <div class="dashboard-container">
        @include('layouts._partials.sidebar')
        <main class="main-content">
            @include('layouts._partials.topbar')
            <div class="profile">
                <span class="profile-icon">
                    <img src="{{ asset('images/user.svg') }}" alt="Profile" class="profile-img">
                </span>
                Hi, Admin!
            </div>
            @yield('content')
            <div class="copyright">
                Copyright 2025 PT Garuda Cyber Indonesia
            </div>
            <div class="dashboard-bg-motif"></div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>