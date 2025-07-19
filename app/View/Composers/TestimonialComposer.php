<?php

namespace App\View\Composers;

use App\Models\Testimoni;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class TestimonialComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Cache the testimonial for 60 seconds (1 minute).
        $randomTestimonial = Cache::remember('random_testimonial', 60, function () {
            // Get one random testimonial that is published.
            return Testimoni::where('status', 'published')->whereNull('product_id')->inRandomOrder()->first();
        });

        // Pass the testimonial data to the view.
        $view->with('randomTestimonial', $randomTestimonial);
    }
}
