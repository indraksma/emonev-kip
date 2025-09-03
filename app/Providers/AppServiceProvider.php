<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated; 

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
        Route::middleware(['web', 'admin'])
            ->prefix('admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            // Jika yang mencoba login adalah admin, arahkan ke dashboard admin
            if ($request->route()->getPrefix() === '/admin') {
                return route('dashboard'); // Nama rute dashboard admin
            }

            // Jika yang mencoba login adalah user biasa, arahkan ke dashboard user
            // Ganti '/dashboard-user' dengan rute dashboard user Anda
            return '/dashboard'; 
        });
    }
}
