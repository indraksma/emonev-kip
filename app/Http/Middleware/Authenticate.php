<?php
// Lokasi file: app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request tidak mengharapkan response JSON
        if (! $request->expectsJson()) {
            
            // Logika Cerdas: Cek apakah URL yang diakses berawalan "admin/"
            if ($request->is('admin/*')) {
                // Jika ya, maka arahkan pengguna ke halaman login admin
                return route('admin.login');
            }
            
            // Jika tidak, arahkan ke halaman login user biasa (default)
            return route('login');
        }

        return null;
    }
}