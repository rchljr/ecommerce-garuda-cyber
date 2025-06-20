<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Toko Online</title>
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom styles for the search bar background */
        .search-bg-blue {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .search-bg-blue::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        /* Custom styles for chart container to prevent height issues */
        .dashboard-chart-card canvas {
            max-height: 180px; /* Ensure chart respects its container height */
        }
    </style>
</head>

<body class="font-sans bg-gray-50 text-gray-800">
    <div class="flex flex-col min-h-screen">

        <header class="bg-blue-600 p-4 shadow-md z-10">
            <nav class="flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center space-x-4">
                    <a href="#" class="flex items-center text-white text-2xl font-bold">
                        <svg class="h-8 w-8 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Nama Dashboard
                    </a>

                    <button id="mobile-menu-button" class="text-white lg:hidden focus:outline-none ml-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="Search..."
                            class="search-bg-blue w-64 pl-10 pr-4 py-2 rounded-md text-white border border-transparent focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-blue-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <button class="relative p-2 text-white hover:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300 rounded-full">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.002 2.002 0 0118 14.059V11c0-3.313-2.687-6-6-6S6 7.687 6 11v3.059c0 .762-.312 1.488-.867 2.043L4 17h5m6 0a3 3 0 11-6 0m6 0v1a3 3 0 11-6 0v-1">
                            </path>
                        </svg>
                    </button>

                    <a href="#" class="p-2 text-white hover:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300 rounded-full">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </nav>
        </header>

        <div class="flex flex-1">

            <aside id="sidebar" class="w-60 bg-gray-900 text-gray-100 p-6 flex-col hidden lg:flex">
                <div class="text-center mb-8 pb-4 border-b border-gray-700">
                    <h3 class="text-2xl font-semibold text-white">Toko Online</h3>
                </div>
                <nav>
                    <ul class="list-none p-0 m-0">
                        <li><a href="#" data-content-id="dashboard" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Dashboard</a></li>
                        {{-- Menggunakan route() helper untuk menunjuk ke rute yang mengembalikan partial view --}}
                        <li><a href="#" data-content-id="products" data-file="{{ route('dashboard-mitra.products.content') }}" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Produk</a></li>
                         <li><a href="#" data-content-id="slides" data-file="{{ route('dashboard-mitra.slides.content') }}" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Slide</a></li>
                        {{-- <li><a href="#" data-content-id="orders" data-file="{{ route('dashboard-mitra.orders.content') }}" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Pesanan</a></li>
                        <li><a href="#" data-content-id="customers" data-file="{{ route('dashboard-mitra.customers.content') }}" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Pelanggan</a></li>
                        <li><a href="#" data-content-id="reports" data-file="{{ route('dashboard-mitra.reports.content') }}" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Laporan</a></li>
                        <li><a href="#" data-content-id="settings" data-file="{{ route('dashboard-mitra.settings.content') }}" class="sidebar-link block text-gray-100 no-underline py-3 px-4 rounded-md mb-1 hover:bg-gray-700 transition-colors duration-300">Pengaturan</a></li> --}}
                    </ul>
                </nav>
            </aside>

            <main id="main-content-area" class="flex-grow p-8">
                </main>
        </div>

        <footer class="bg-gray-800 text-white p-4 text-center text-sm">
            <p>&copy; 2025 Toko Online Anda</p>
        </footer>

    </div>

    <script>
        // Get references to DOM elements
        const mainContentArea = document.getElementById('main-content-area');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');

        // Store Chart.js instance to destroy it before re-initializing
        let myChart = null;

        // --- Content Definitions ---
        // Map content IDs to their respective file paths or direct HTML for dashboard
        const contentMap = {
            dashboard: `
                <h1 class="text-3xl font-bold mb-6 text-gray-800">Dashboard Utama</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-5 rounded-lg shadow-lg text-white flex items-center justify-between transform transition duration-300 hover:scale-105">
                        <div>
                            <p class="text-sm opacity-90">Total Penjualan</p>
                            <p class="text-3xl font-bold mt-1">Rp 12.500.000</p>
                        </div>
                        <svg class="h-10 w-10 opacity-75" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 20.5c-.28 0-.5.22-.5.5s.22.5.5.5.5-.22.5-.5-.22-.5-.5-.5zM9 20.5c-.28 0-.5.22-.5.5s.22.5.5.5.5-.22.5-.5-.22-.5-.5-.5zM20 16V4H4v12H2v2h2v2c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-2h2v-2h-2zM6 18c0-.55.45-1 1-1h10c.55 0 1 .45 1 1v1c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-1zm12-3V6H6v9h12zM9 13c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1s1 .45 1 1v3c0 .55-.45 1-1 1zm3 0c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1s1 .45 1 1v3c0 .55-.45 1-1 1zm3 0c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1s1 .45 1 1v3c0 .55-.45 1-1 1z"></path>
                        </svg>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-5 rounded-lg shadow-lg text-white flex items-center justify-between transform transition duration-300 hover:scale-105">
                        <div>
                            <p class="text-sm opacity-90">Pesanan Baru</p>
                            <p class="text-3xl font-bold mt-1">45</p>
                        </div>
                        <svg class="h-10 w-10 opacity-75" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 11c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm-6 0c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm14-4V2H3v5H1v15h22V7h-2zM5 4h14v3H5V4zm16 16H3V9h18v11z"></path>
                        </svg>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-5 rounded-lg shadow-lg text-white flex items-center justify-between transform transition duration-300 hover:scale-105">
                        <div>
                            <p class="text-sm opacity-90">Produk Terjual</p>
                            <p class="text-3xl font-bold mt-1">120</p>
                        </div>
                        <svg class="h-10 w-10 opacity-75" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15.5c-3.03 0-5.5-2.47-5.5-5.5S8.97 6.5 12 6.5s5.5 2.47 5.5 5.5-2.47 5.5-5.5 5.5zm-1-8.5v-2h2v2h-2zm0 4h2v-2h-2v2z"></path>
                        </svg>
                    </div>

                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-5 rounded-lg shadow-lg text-white flex items-center justify-between transform transition duration-300 hover:scale-105">
                        <div>
                            <p class="text-sm opacity-90">Pelanggan Baru</p>
                            <p class="text-3xl font-bold mt-1">15</p>
                        </div>
                        <svg class="h-10 w-10 opacity-75" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg mb-8 dashboard-chart-card">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Penjualan Bulanan</h2>
                    <canvas id="chart" class="w-full"></canvas>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Pesanan Terbaru</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Budi Santoso</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rp 250.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Siti Aminah</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rp 150.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Tertunda</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P003</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Joko Susilo</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rp 300.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P004</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Dewi Sartika</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rp 80.000</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `,
        };

        function initializeChart() {
            const ctx = document.getElementById('chart');
            if (myChart) {
                myChart.destroy();
            }

            if (ctx) {
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                        datasets: [{
                            label: 'Paket Terjual',
                            data: [32, 42, 38, 45, 41, 36, 50, 55, 44, 47, 23, 40],
                            backgroundColor: '#60C12C',
                            borderRadius: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.7)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                displayColors: false,
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6b7280'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e5e7eb'
                                },
                                ticks: {
                                    color: '#6b7280'
                                }
                            }
                        }
                    }
                });
            }
        }

        async function loadContent(contentId, filePath = null) {
            if (contentId === 'dashboard') {
                mainContentArea.innerHTML = contentMap.dashboard;
                initializeChart();
            } else if (filePath) {
                try {
                    const response = await fetch(filePath);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const htmlContent = await response.text();
                    mainContentArea.innerHTML = htmlContent;

                    // Execute scripts loaded within the content
                    const scripts = mainContentArea.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        if (oldScript.src) {
                            newScript.src = oldScript.src;
                        } else {
                            newScript.textContent = oldScript.textContent;
                        }
                        // Replace the old script with the new one to ensure execution
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });


                } catch (error) {
                    console.error('Error loading content:', error);
                    mainContentArea.innerHTML = `<p class="text-red-500">Gagal memuat konten. Silakan coba lagi nanti.</p>`;
                }
            } else {
                mainContentArea.innerHTML = `<p>Konten untuk "${contentId}" tidak ditemukan.</p>`;
            }
        }

        function setActiveLink(clickedLink) {
            sidebarLinks.forEach(link => {
                link.classList.remove('bg-teal-500', 'text-white');
                link.classList.add('hover:bg-gray-700');
            });
            clickedLink.classList.remove('hover:bg-gray-700');
            clickedLink.classList.add('bg-teal-500', 'text-white');
        }

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const contentId = this.dataset.contentId;
                const filePath = this.dataset.file;

                // Remove existing scripts to prevent duplication before loading new content
                const existingDynamicScripts = mainContentArea.querySelectorAll('script');
                existingDynamicScripts.forEach(script => script.remove());

                loadContent(contentId, filePath);
                setActiveLink(this);
            });
        });

        mobileMenuButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
        });

        document.addEventListener('DOMContentLoaded', () => {
            const dashboardLink = document.querySelector('.sidebar-link[data-content-id="dashboard"]');
            if (dashboardLink) {
                loadContent('dashboard');
                setActiveLink(dashboardLink);
            }
        });
    </script>
</body>

</html>