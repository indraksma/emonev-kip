<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// We are now making this a full-page component by removing the layout attribute
// and adding the full HTML structure below.
new class extends Component
{
    //
}; ?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Berhasil - E-Monev Banjarnegara</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center items-center p-4">
        <div class="max-w-md w-full text-center">

            <div class="bg-white p-8 rounded-lg shadow-md">
                <div class="flex justify-start mb-8">
                    <a href="/" class="flex items-center space-x-2">
                        <img src="/images/logobna.png" alt="Logo E-Monev" class="h-10 w-auto">
                        <span class="text-xl font-bold text-gray-800">E-Monev</span>
                    </a>
                </div>
                <!-- Ganti 'celebrate.gif' dengan nama file GIF Anda -->
                <img src="/images/celebrate.gif" alt="Success GIF" class="mx-auto mb-6 w-48 h-auto">

                <h1 class="text-3xl font-bold text-blue-600">Congratulations!</h1>
                <p class="mt-2 text-gray-600">Horee, Kamu Berhasil Merubah Password</p>

                <div class="mt-8">
                    <a href="{{ route('login') }}" wire:navigate class="w-full inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline">
                        Kembali ke Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
