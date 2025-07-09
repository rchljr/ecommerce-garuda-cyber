<?php

namespace App\Providers;

use App\Listeners\MergeCartOnLogin; // Import listener
use Illuminate\Auth\Events\Login; // Import event
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ... listener lain

        // Tambahkan ini
        Login::class => [
            MergeCartOnLogin::class,
        ],
    ];

    // ...
}