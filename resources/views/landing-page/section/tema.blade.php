{{-- shop theme section --}}

<section id="tema" class="theme-section">
    <h1 class="theme-title">Tema Toko Online Anda</h1>
    <div class="theme-box">
        <div class="theme-cards">
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme1.jpg" alt="Tema 1"></div>
                <div class="theme-name">Coming Soon Landing Page</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme2.jpg" alt="Tema 2"></div>
                <div class="theme-name">Beauty Salon</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 3"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 4"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 5"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 6"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 7"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 8"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 9"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 10"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 11"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <div class="theme-card">
                <div class="theme-img"><img src="images/theme3.jpg" alt="Tema 12"></div>
                <div class="theme-name">Construction Company</div>
            </div>
            <!-- Tambah theme-card sesuai kebutuhan -->
        </div>
        <div class="theme-nav">
            <button class="theme-btn prev">&lt;</button>
            <button class="theme-btn next">&gt;</button>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.querySelector('.theme-btn.prev').onclick = function () {
            document.querySelector('.theme-cards').scrollBy({ left: -250, behavior: 'smooth' });
        };
        document.querySelector('.theme-btn.next').onclick = function () {
            document.querySelector('.theme-cards').scrollBy({ left: 250, behavior: 'smooth' });
        };
    </script>
@endpush