<aside class="sidebar">
    <nav class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="sidebar-item">
            <span class="sidebar-icon">
                @include('icons.dashboard')
            </span>
            Dashboard
        </a>
        <div class="sidebar-dropdown">
            <button class="sidebar-item dropdown-toggle" type="button">
                <span class="sidebar-icon">@include('icons.mitra')</span>
                Mitra
            </button>
            <div class="sidebar-submenu">
                <a href="{{ route('verifikasi-mitra') }}" class="sidebar-subitem">Verifikasi Mitra</a>
                <a href="{{ route('kelola-mitra') }}" class="sidebar-subitem">Kelola Mitra</a>
            </div>
        </div>

        <a href="{{ route('kelola-paket') }}" class="sidebar-item">
            <span class="sidebar-icon">
                @include('icons.paket')
            </span>
            Kelola Paket
        </a>
        <a href="{{ route('kelola-voucher') }}" class="sidebar-item">
            <span class="sidebar-icon">
                @include('icons.voucher')
            </span>
            Kelola Voucher
        </a>
        <a href="{{ route('kelola-testimoni') }}" class="sidebar-item">
            <span class="sidebar-icon">
                @include('icons.testimoni')
            </span>
            Kelola Testimoni
        </a>
        <a href="{{ route('kelola-pendapatan') }}" class="sidebar-item">
            <span class="sidebar-icon">
                @include('icons.pendapatan')
            </span>
            Kelola Pendapatan
        </a>
        <a href="{{ route('kelola-landing') }}" class="sidebar-item">
            <span class="sidebar-icon">
                @include('icons.landing')
            </span>
            Landing Page
        </a>
    </nav>
    <div class="sidebar-footer">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="sidebar-logout"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="sidebar-icon">
                @include('icons.logout')
            </span>
            Logout
        </a>
    </div>

</aside>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown .dropdown-toggle');
            const sidebarItems = document.querySelectorAll('.sidebar-item, .sidebar-subitem');

            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const parent = this.parentElement;
                    parent.classList.toggle('open');
                });
            });

            function clearActive() {
                sidebarItems.forEach(item => item.classList.remove('active'));
                dropdownToggles.forEach(toggle => toggle.classList.remove('active'));
                document.querySelectorAll('.sidebar-dropdown').forEach(drop => drop.classList.remove('open'));
            }

            function setActiveFromPathname() {
                const currentPath = window.location.pathname;
                let found = false;

                sidebarItems.forEach(item => {
                    const href = item.getAttribute('href');
                    if (!href) return;

                    const link = document.createElement('a');
                    link.href = href;
                    const linkPath = link.pathname;

                    if (linkPath === currentPath) {
                        clearActive();
                        item.classList.add('active');

                        const dropdownParent = item.closest('.sidebar-dropdown');
                        if (dropdownParent) {
                            const toggleBtn = dropdownParent.querySelector('.dropdown-toggle');
                            if (toggleBtn) {
                                toggleBtn.classList.add('active');
                                dropdownParent.classList.add('open');
                            }
                        }
                        found = true;
                    }
                });

                if (!found) {
                    clearActive();
                    const dashboardLink = document.querySelector('.sidebar-item[href="{{ route('dashboard') }}"]');
                    if (dashboardLink) dashboardLink.classList.add('active');
                }
            }

            setActiveFromPathname();

            window.addEventListener('popstate', setActiveFromPathname);
        });


    </script>

@endpush