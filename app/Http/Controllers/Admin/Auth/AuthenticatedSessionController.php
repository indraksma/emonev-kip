<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // <-- Tambahkan ini

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function create(): View
    {
        return view('admin.auth.login'); 
    }

    /**
     * Menangani permintaan otentikasi yang masuk.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Coba untuk melakukan otentikasi
        if (! Auth::guard('admin')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {

            // --- INI BAGIAN PENTINGNYA ---
            // Jika otentikasi GAGAL, lemparkan error dan kembali ke form login admin.
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Jika otentikasi BERHASIL, regenerasi session
        $request->session()->regenerate();

        // Arahkan ke dashboard admin
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Menghancurkan sesi otentikasi (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}