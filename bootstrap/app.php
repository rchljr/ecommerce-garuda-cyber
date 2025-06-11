<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


require_once __DIR__ . '/../vendor/autoload.php';

// Buat instance aplikasi Laravel
$app = Application::configure(basePath: dirname(__DIR__));

// Daftarkan middleware route alias
$app->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => CheckRole::class,
        // Anda bisa daftarkan alias middleware lain di sini
    ]);
});

// Konfigurasi routing dan lainnya
$app = $app->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
)->withMiddleware(function (Middleware $middleware) {
    // Tambahkan global middleware jika perlu, contoh:
    // $middleware->append(\App\Http\Middleware\TrustProxies::class);
})->withExceptions(function (Exceptions $exceptions) {
    // Konfigurasi exception handler jika perlu
})->create();

return $app;