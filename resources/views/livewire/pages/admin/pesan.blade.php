<?php

use Livewire\Volt\Component;
use App\Models\Pesan;
use App\Models\User;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    public $judul = '';
    public $isi = '';

    public function kirimPesan()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        // Buat pesan baru
        $pesan = Pesan::create([
            'judul' => $this->judul,
            'isi' => $this->isi,
        ]);

        // Lampirkan pesan ini ke semua user 'dinas'
        $users = User::where('role', 'dinas')->get();
        $pesan->users()->attach($users->pluck('id'));

        // Reset form dan beri notifikasi sukses
        $this->reset(['judul', 'isi']);
        session()->flash('success', 'Pesan notifikasi berhasil dikirim ke semua PPID Pelaksana.');
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-8">
            <h1 class="text-3xl font-bold text-gray-900">Pesan</h1>
            <div class="relative">
                <input type="text" placeholder="Cari sesuatu..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </x-slot>

    <main class="p-8">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form wire:submit="kirimPesan">
                <div class="border-b pb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Kirim Pesan Notifikasi</h2>
                    <p class="mt-1 text-sm text-gray-600">Kirim pemberitahuan penting kepada seluruh Badan Publik / PPID Pelaksana yang terdaftar di sistem E-Monev.</p>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Notifikasi</label>
                        <input wire:model="judul" id="judul" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('judul') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="isi" class="block text-sm font-medium text-gray-700">Isi Pesan</label>
                        <textarea wire:model="isi" id="isi" rows="8" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('isi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
