<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\TemaComposer; // <-- IMPORT COMPOSER ANDA

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Menjalankan TemaComposer setiap kali layout template1 dipanggil
        View::composer('template1.layouts.template', TemaComposer::class);
        View::composer('template2.layouts.template2', TemaComposer::class); 
    }
}