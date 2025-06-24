<!-- Add Testimonial Section -->
<section id="add-testimonial" class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-900">Bagikan Pengalaman Anda</h2>
            <p class="mt-4 text-gray-600">Kami sangat menghargai masukan Anda. Berikan testimoni Anda untuk membantu kami menjadi lebih baik.</p>
        </div>

        <div class="mt-12 max-w-2xl mx-auto">
            @if(session('testimonial_success'))
                {{-- Tampilan setelah berhasil submit --}}
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded-lg text-center">
                    <h3 class="font-bold text-xl mb-2">Terima Kasih!</h3>
                    <p>{{ session('testimonial_success') }}</p>
                </div>
            @else
                {{-- Tampilan form --}}
                <form action="{{ route('testimonials.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    {{-- Nama Pengguna --}}
                    {{-- Jika pengguna sudah login, nama akan terisi otomatis dan bisa diedit --}}
                    @auth
                        <div>
                            <label for="name" class="block text-base font-semibold text-gray-800 mb-1">Nama Anda</label>
                            <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white focus:border-red-600 focus:ring-0 transition">
                        </div>
                    @else
                        <div>
                            <label for="name" class="block text-base font-semibold text-gray-800 mb-1">Nama Anda<span class="text-red-600">*</span></label>
                            <input type="text" id="name" name="name" class="block w-full h-12 px-4 border-2 border-gray-300 rounded-lg bg-white focus:border-red-600 focus:ring-0 transition" required>
                            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endauth

                    {{-- Rating Bintang --}}
                    <div>
                        <label class="block text-base font-semibold text-gray-800 mb-2">Rating Anda<span class="text-red-600">*</span></label>
                        <div id="testimonial-star-rating" class="flex items-center text-4xl text-gray-300">
                            <span class="testimonial-star cursor-pointer" data-value="1">★</span>
                            <span class="testimonial-star cursor-pointer" data-value="2">★</span>
                            <span class="testimonial-star cursor-pointer" data-value="3">★</span>
                            <span class="testimonial-star cursor-pointer" data-value="4">★</span>
                            <span class="testimonial-star cursor-pointer" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="rating" id="testimonial_rating_value" value="0" required>
                        @error('rating')<p class="text-red-600 text-sm mt-1">Silakan berikan rating.</p>@enderror
                    </div>

                    {{-- Isi Testimoni --}}
                    <div>
                        <label for="content" class="block text-base font-semibold text-gray-800 mb-1">Testimoni Anda<span class="text-red-600">*</span></label>
                        <textarea id="content" name="content" rows="5" class="block w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:ring-0 transition" required></textarea>
                        @error('content')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition-colors text-lg">Kirim Testimoni</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starContainer = document.getElementById('testimonial-star-rating');
    if (!starContainer) return;

    const ratingInput = document.getElementById('testimonial_rating_value');
    const stars = starContainer.querySelectorAll('.testimonial-star');
    let currentRating = 0;

    const updateStars = (rating) => {
        stars.forEach(star => {
            const starValue = parseInt(star.dataset.value);
            star.classList.toggle('text-yellow-400', starValue <= rating);
            star.classList.toggle('text-gray-300', starValue > rating);
        });
    };

    stars.forEach(star => {
        star.addEventListener('mouseover', () => updateStars(star.dataset.value));
        star.addEventListener('mouseout', () => updateStars(currentRating));
        star.addEventListener('click', () => {
            currentRating = parseInt(star.dataset.value);
            ratingInput.value = currentRating;
            updateStars(currentRating);
        });
    });
});
</script>
@endpush
