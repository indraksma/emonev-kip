<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// Menggunakan layout admin yang sudah ada
new #[Layout('components.layouts.admin')] class extends Component
{

}; ?>

<div>
    <!-- Header Halaman -->
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">Keluar</h1>
    </x-slot>

    <!-- Konten Utama -->
    <main class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md text-center bg-white p-10 rounded-xl shadow-lg">
            <!-- Anda bisa menggunakan ilustrasi jika punya -->
            <img src="/images/logout.png" alt="Logout Illustration" class="mx-auto mb-8 w-64 h-auto">
            
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Kamu Yakin Mau Keluar?</h2>
            <p class="text-gray-500 mb-8">Anda akan keluar dari sesi admin dan perlu login kembali untuk masuk.</p>

            <div class="flex justify-center space-x-4">
                <!-- Tombol Batal: Kembali ke dashboard -->
                <a href="{{ route('admin.dashboard') }}" wire:navigate 
                    class="px-8 py-3 font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition w-32">
                    Batal
                </a>

                <!-- Tombol Keluar: Menjalankan aksi logout -->
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" 
                            class="px-8 py-3 font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition w-32">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
