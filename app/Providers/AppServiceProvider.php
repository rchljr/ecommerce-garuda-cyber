<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\TestimonialComposer;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\CustomTema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.auth', TestimonialComposer::class);
        View::composer('*', function ($view) {
        $view->with('contact', Contact::first());
    });
    View::composer('*', function ($view) {
        $user = Auth::user();
        $customTema = $user ? CustomTema::where('user_id', $user->id)->first() : null;
        $view->with('customTema', $customTema);
    });
    }
}
