<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route; 

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            // Daftarkan file rute web standar
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Daftarkan file rute admin Anda di sini
            Route::middleware('web')
                // tambahkan prefix agar semua url admin menjadi /admin/...
                ->prefix('admin')
                // tambahkan nama agar route name menjadi admin.nama_rute
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth'   => \App\Http\Middleware\Authenticate::class,
            'admin'  => \App\Http\Middleware\AdminMiddleware::class,
            'guest'  => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();