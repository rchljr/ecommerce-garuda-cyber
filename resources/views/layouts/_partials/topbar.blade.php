<header class="topbar">
    <div class="topbar-left">
        <img src="{{ asset('images/logogci.png') }}" alt="Garuda Cyber" class="topbar-logo">
        <button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Toggle Sidebar">
            <img src="{{ asset('images/sidebar.png') }}" alt="Toggle Sidebar" class="sidebar-toggle-icon" />
        </button>
    </div>
    <div class="search-bar">
        <input type="text" placeholder="Search">
        <button type="button">&#128269;</button>
    </div>
</header>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            sidebarToggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
            });
        });
    </script>
@endpush