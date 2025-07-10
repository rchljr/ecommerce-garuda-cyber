<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


// Buat instance aplikasi Laravel
$app = Application::configure(basePath: dirname(__DIR__));

// Daftarkan middleware route alias
$app->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        'tenant.exist' => \App\Http\Middleware\EnsureTenantExists::class,
        'set-tenant' => \App\Http\Middleware\SetTenantFromAuth::class,
    ]);
});

// Konfigurasi routing dan lainnya
$app = $app->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
)->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        // \App\Http\Middleware\Authenticate::class, // Middleware untuk autentikasi
    ])->api(append: [
        // Tambahkan middleware API jika diperlukan
    ]);
})->withExceptions(function (Exceptions $exceptions) {
    // Konfigurasi exception handler jika perlu
})->create();

return $app;