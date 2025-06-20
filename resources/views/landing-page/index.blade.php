@extends('layouts.landing')
@section('title')
@section('content')
    @include('landing-page.section.banner')
    @include('landing-page.section.layanan')
    @include('landing-page.section.highlight')
    @include('landing-page.section.tema')
    @include('landing-page.section.paket')
    @include('landing-page.section.slogan')
    @include('landing-page.section.faq')
    @include('landing-page.section.testimoni')
    @include('landing-page.section.add-testimoni')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Navbar Mobile
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Fungsi Generic untuk Carousel
            function setupCarousel(carouselId, prevBtnId, nextBtnId) {
                const carousel = document.getElementById(carouselId);
                const prevBtn = document.getElementById(prevBtnId);
                const nextBtn = document.getElementById(nextBtnId);

                if (!carousel || !prevBtn || !nextBtn) return;

                // Menggunakan clientWidth dari kontainer untuk jumlah scroll yang lebih konsisten
                const scrollAmount = carousel.clientWidth;

                prevBtn.addEventListener('click', () => {
                    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                });
                nextBtn.addEventListener('click', () => {
                    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                });
            }

            // Setup Carousel Tema
            setupCarousel('tema-carousel', 'tema-prev-btn', 'tema-next-btn');
            // Setup Carousel Testimoni
            setupCarousel('testimoni-carousel', 'testimoni-prev-btn', 'testimoni-next-btn');

            // FAQ Accordion
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                const icon = item.querySelector('.faq-icon');

                question.addEventListener('click', () => {
                    const isHidden = answer.classList.contains('hidden');

                    // Tutup semua jawaban lain untuk efek akordion sejati
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.querySelector('.faq-answer').classList.add('hidden');
                            otherItem.querySelector('.faq-icon').classList.remove('rotate-45');
                        }
                    });

                    // Buka/tutup jawaban yang diklik
                    answer.classList.toggle('hidden');
                    icon.classList.toggle('rotate-45');
                });
            });

            // Toggle Harga Paket
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const starterPriceEl = document.getElementById('starter-price');
            const businessPriceEl = document.getElementById('business-price');
            const starterPeriodEl = document.getElementById('starter-period');
            const businessPeriodEl = document.getElementById('business-period');

            const prices = {
                monthly: {
                    starter: 'Rp150.000',
                    business: 'Rp300.000',
                    periodText: 'Per pengguna/bulan, ditagih bulanan'
                },
                yearly: {
                    starter: 'Rp1.500.000',
                    business: 'Rp3.000.000',
                    periodText: 'Per pengguna/tahun, ditagih tahunan'
                }
            };

            function setPricing(period) {
                starterPriceEl.textContent = prices[period].starter;
                businessPriceEl.textContent = prices[period].business;

                starterPeriodEl.textContent = prices[period].periodText;
                businessPeriodEl.textContent = prices[period].periodText;
            }

            monthlyBtn.addEventListener('click', () => {
                setPricing('monthly');
                monthlyBtn.classList.remove('bg-transparent', 'text-gray-800');
                monthlyBtn.classList.add('bg-red-600', 'text-white', 'shadow-md');

                yearlyBtn.classList.remove('bg-red-600', 'text-white', 'shadow-md');
                yearlyBtn.classList.add('bg-transparent', 'text-gray-800');
            });

            yearlyBtn.addEventListener('click', () => {
                setPricing('yearly');
                yearlyBtn.classList.remove('bg-transparent', 'text-gray-800');
                yearlyBtn.classList.add('bg-red-600', 'text-white', 'shadow-md');

                monthlyBtn.classList.remove('bg-red-600', 'text-white', 'shadow-md');
                monthlyBtn.classList.add('bg-transparent', 'text-gray-800');
            });

        });
    </script>
@endpush
