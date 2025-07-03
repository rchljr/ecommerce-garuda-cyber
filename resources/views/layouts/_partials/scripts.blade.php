<!-- Javascript files-->
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.0.0.min.js"></script>
<script src="js/plugin.js"></script>
<!-- sidebar -->
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="js/custom.js"></script>
<!-- javascript -->
<script>
    // Material Select Initialization
    $(document).ready(function () {
        $('.mdb-select').materialSelect();
        $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
            $(this).closest('.select-outline').find('label').toggleClass('active');
            $(this).closest('.select-outline').find('.caret').toggleClass('active');
        });
    });
</script>
<script>
    window.addEventListener('scroll', function () {
        const header = document.querySelector('.header_section');
        if (window.scrollY > 50) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }
    });

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const dropdownToggles = document.querySelectorAll('.sidebar-dropdown .dropdown-toggle');
        const sidebarItems = document.querySelectorAll('.sidebar-item, .sidebar-subitem');

        // Toggle Sidebar collapsed
        if (sidebarToggleBtn && sidebar) {
            sidebarToggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Dropdown Menu
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.parentElement;

                // Jika sidebar collapsed, jangan buka dropdown tapi tetap navigasi (jika ada link)
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                }

                parent.classList.toggle('open');
                const submenu = parent.querySelector('.sidebar-submenu');
                submenu.classList.toggle('hidden');
            });
        });

        // Set Active Menu Item
        function setActiveMenu() {
            const currentPath = window.location.pathname;

            sidebarItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.startsWith(new URL(href).pathname)) {
                    item.classList.add('bg-red-800', 'font-bold');

                    const dropdownParent = item.closest('.sidebar-dropdown');
                    if (dropdownParent) {
                        dropdownParent.querySelector('.dropdown-toggle').classList.add('bg-red-800', 'font-bold');
                        dropdownParent.querySelector('.sidebar-submenu').classList.remove('hidden');
                    }
                } else {
                    item.classList.remove('bg-red-800', 'font-bold');
                }
            });
        }

        setActiveMenu();
    });
</script>
<script>
    /**
     * Inisialisasi fungsionalitas pencarian untuk tabel.
     *
     * Fungsi ini mencari elemen-elemen berikut berdasarkan ID dan Class:
     * - Tombol Pencarian: #search-btn
     * - Input Pencarian: #table-search-input
     * - Kontainer Judul: #header-title
     * - Table Body: .searchable-table
     * - Baris Data: .search-row
     * - Baris 'Tidak Ditemukan': .no-results-row
     */
    function initializeTableSearch() {
        const searchBtn = document.getElementById('search-btn');
        const searchInput = document.getElementById('table-search-input');
        const headerTitle = document.getElementById('header-title');
        const searchableTable = document.querySelector('.searchable-table');

        // Jika elemen-elemen penting tidak ditemukan di halaman, hentikan eksekusi.
        if (!searchBtn || !searchInput || !headerTitle || !searchableTable) {
            return;
        }

        const noResultsRow = searchableTable.querySelector('.no-results-row');

        // Event saat tombol ikon pencarian diklik
        searchBtn.addEventListener('click', () => {
            headerTitle.classList.toggle('hidden');
            searchInput.classList.toggle('hidden');

            if (!searchInput.classList.contains('hidden')) {
                searchInput.focus();
            } else {
                // Reset pencarian saat input disembunyikan
                searchInput.value = '';
                searchableTable.querySelectorAll('.search-row').forEach(row => {
                    row.style.display = '';
                });
                if (noResultsRow) {
                    noResultsRow.classList.add('hidden');
                }
            }
        });

        // Event saat pengguna mengetik di kolom pencarian
        searchInput.addEventListener('keyup', () => {
            const filter = searchInput.value.toUpperCase();
            const rows = searchableTable.querySelectorAll('.search-row');
            let visibleRowCount = 0;

            rows.forEach(row => {
                const textValue = row.textContent || row.innerText;
                if (textValue.toUpperCase().indexOf(filter) > -1) {
                    row.style.display = '';
                    visibleRowCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Tampilkan atau sembunyikan pesan 'tidak ditemukan'
            if (noResultsRow) {
                noResultsRow.classList.toggle('hidden', visibleRowCount > 0);
            }
        });
    }
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
{{-- Muat SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{!! showAlert() !!}
{!! deleteConfirmScript() !!}
{!! deactivateConfirmScript() !!} 
{!! logoutConfirmScript() !!}
@stack('scripts')