<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    @include('layouts._partials.head')
    @push('styles')
        <style>
            /* Menambahkan gaya untuk link navigasi yang aktif */
            .nav-link.active {
                color: #DC2626;
                /* Warna merah-600 */
                font-weight: 700;
            }

            .nav-link-mobile.active {
                color: #DC2626;
                /* Warna merah-600 */
                background-color: #FEF2F2;
                /* Warna red-50 */
                font-weight: 600;
            }
        </style>
    @endpush
</head>

<body class="bg-white-50 text-gray-800">

    @include('layouts._partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('layouts._partials.footer')

    @include('layouts._partials.scripts')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Logika untuk menu mobile
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenuButton) {
                    mobileMenuButton.addEventListener('click', () => {
                        mobileMenu.classList.toggle('hidden');
                    });
                }

                // Logika untuk menandai link aktif saat scroll
                const sections = document.querySelectorAll('section[id]');
                const navLinksDesktop = document.querySelectorAll('#desktop-nav a.nav-link');
                const navLinksMobile = document.querySelectorAll('#mobile-menu a.nav-link-mobile');

                if (sections.length > 0 && navLinksDesktop.length > 0) {
                    const observerOptions = {
                        root: null,
                        rootMargin: '0px',
                        threshold: 0.5
                    };

                    const observer = new IntersectionObserver((entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const id = entry.target.getAttribute('id');

                                navLinksDesktop.forEach(link => {
                                    link.classList.remove('active');
                                    if (link.getAttribute('href') === `#${id}`) {
                                        link.classList.add('active');
                                    }
                                });

                                navLinksMobile.forEach(link => {
                                    link.classList.remove('active');
                                    if (link.getAttribute('href') === `#${id}`) {
                                        link.classList.add('active');
                                    }
                                });
                            }
                        });
                    }, observerOptions);

                    sections.forEach(section => {
                        observer.observe(section);
                    });
                }
            });
        </script>
    @endpush
</body>

</html>