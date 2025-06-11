<div class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brandnav">
                <div class="footer-logo">
                    <img src="{{ asset('images/logosabi.png') }}" alt="Garuda Cyber" />
                </div>
                <div class="footer-nav">
                    <span class="footer-nav-title">Navigasi</span>
                    <ul>
                        <li><a href="{{ route('beranda') }}#beranda">Beranda</a></li>
                        <li><a href="{{ route('beranda') }}#layanan">Layanan</a></li>
                        <li><a href="{{ route('beranda') }}#faq">FAQs</a></li>
                        <li><a href="{{ route('beranda') }}#testimoni">Testimoni</a></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider" />
            <div class="w-100 d-flex justify-content-between">
                <form class="footer-contact-form">
                    <label for="footer-email" class="footer-contact-label">Contact Us</label>
                    <input type="email" id="footer-email" class="footer-contact-input"
                        placeholder="Enter your email Address" />
                    <button type="submit" class="footer-contact-btn">GO!</button>
                </form>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">
                        <img src="{{asset('images/ft-fb.png') }}" alt="Facebook" />
                    </a>
                    <a href="#" aria-label="Instagram">
                        <img src="{{asset('images/ft-ig.png') }}" alt="Instagram" />
                    </a>
                    <a href="#" aria-label="Tiktok">
                        <img src="{{asset('images/ft-tt.png') }}" alt="Tiktok" />
                    </a>
                    <a href="#" aria-label="Twitter">
                        <img src="{{asset('images/ft-x.png') }}" alt="Twitter" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom mt-4">
        <span>© 2025 PT. Garuda Cyber Indonesia</span>
    </div>
</div>