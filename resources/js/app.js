/**
 * File JavaScript Utama Aplikasi.
 *
 * File ini di-bundle oleh Vite dan akan memuat semua library JavaScript
 * yang dibutuhkan oleh aplikasi Anda dari node_modules.
 */

// -----------------------------------------------------------------------------
// 1. Impor Library Inti (Core Libraries)
// -----------------------------------------------------------------------------

// Impor Popper.js terlebih dahulu, karena Bootstrap bergantung padanya untuk dropdown, popover, dll.
import '@popperjs/core';

// Impor Bootstrap JavaScript. Ini akan mengaktifkan semua komponen interaktif Bootstrap.
import 'bootstrap';

// Impor jQuery, library utama yang digunakan oleh banyak plugin lama.
import jQuery from 'jquery';

// Impor jQuery Validation untuk validasi form.
import 'jquery-validation';

// Impor SweetAlert2 untuk notifikasi dan popup yang indah.
import Swal from 'sweetalert2';

// Impor Chart.js untuk membuat grafik dan diagram.
import Chart from 'chart.js/auto';

// Impor Axios untuk melakukan request HTTP (AJAX).
import axios from 'axios';


// -----------------------------------------------------------------------------
// 2. Jadikan Library Tersedia Secara Global (Global Availability)
// -----------------------------------------------------------------------------
//
// Langkah ini penting untuk kompatibilitas mundur. Banyak skrip lama
// (terutama dari template) yang mengharapkan library seperti jQuery ($)
// tersedia secara global di objek `window`.
//
// -----------------------------------------------------------------------------

window.$ = window.jQuery = jQuery;
window.Swal = Swal;
window.Chart = Chart;
window.axios = axios;

// Mengatur header CSRF token untuk semua request Axios secara otomatis.
// Ini penting untuk keamanan saat mengirim data ke backend Laravel.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


// -----------------------------------------------------------------------------
// 3. Impor Library Tambahan dari Template
// -----------------------------------------------------------------------------
//
// Library-library ini spesifik untuk template yang Anda gunakan dan
// sekarang dimuat dari node_modules, bukan dari file JS lokal.
//
// -----------------------------------------------------------------------------

import 'owl.carousel';
import 'magnific-popup';
import 'jquery.countdown';
import mixitup from 'mixitup'; // Mixitup perlu diimpor dengan nama variabel
import 'jquery.nicescroll';
import 'slicknav/dist/jquery.slicknav.min.js'; // Path ini umum, sesuaikan jika berbeda


// -----------------------------------------------------------------------------
// 4. Kode Kustom Aplikasi Anda
// -----------------------------------------------------------------------------
//
// Tempatkan semua kode JavaScript kustom Anda di bawah ini.
// Contohnya adalah inisialisasi plugin atau event listener.
//
// -----------------------------------------------------------------------------

console.log('app.js loaded successfully. All libraries are initialized.');

// Inisialisasi plugin setelah DOM (halaman) selesai dimuat.
// Menggunakan $(document).ready() adalah praktik terbaik saat menggunakan jQuery.
$(document).ready(function () {
    console.log('Document is ready, initializing jQuery plugins...');

    // Contoh inisialisasi Owl Carousel (sesuaikan selector dengan template Anda)
    if ($('.owl-carousel').length) {
        $('.owl-carousel').owlCarousel({
            // Opsi Owl Carousel bisa ditambahkan di sini
            // contoh: loop: true, margin: 10, nav: true
        });
    }

    // Contoh inisialisasi Magnific Popup (sesuaikan selector)
    if ($('.image-popup').length) {
        $('.image-popup').magnificPopup({
            type: 'image'
            // Opsi lain bisa ditambahkan di sini
        });
    }

    // Contoh inisialisasi Slicknav (sesuaikan selector)
    if ($('#menu').length) { // Biasanya menargetkan elemen <ul id="menu">
        $('#menu').slicknav();
    }

    // Contoh inisialisasi NiceScroll (biasanya pada body atau elemen scrollable)
    $('html').niceScroll();

    // Contoh inisialisasi MixItUp (sesuaikan selector)
    if ($('.container-mix').length) {
        var mixer = mixitup('.container-mix');
    }

    // Contoh inisialisasi Countdown (sesuaikan selector)
    if ($('#countdown').length) {
        $('#countdown').countdown('2026/01/01', function (event) {
            $(this).html(event.strftime('%D hari %H:%M:%S'));
        });
    }

    // Anda bisa memindahkan fungsi `initializeTableSearch()` dari file Blade ke sini
    // agar lebih terorganisir.
});
