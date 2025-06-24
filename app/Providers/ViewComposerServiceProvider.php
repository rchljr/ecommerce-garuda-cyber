<?php

namespace App\Providers;

use Illuminate\Support\Facades\View; // Import Facade View
use Illuminate\Support\ServiceProvider;
use App\Models\Page; // Import Model Page

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Ini adalah logika View Composer yang sebenarnya
        // yang akan melampirkan variabel 'dynamicPages' ke partial header
        View::composer('template1.partials.header', function ($view) {
            $dynamicPages = Page::where('is_active', true)
                                ->where('show_in_nav', true)
                                ->orderBy('order')
                                ->get();
            
            $view->with('dynamicPages', $dynamicPages);
        });
    }
}