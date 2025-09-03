<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithFileUploads;

    public $name;
    // public $username; // Dihapus karena tidak ada di database
    public $email;
    public $password;
    public $password_confirmation;
    public $photo;
    public $existingPhoto;

    /**
     * Mount the component and load the admin's data.
     */
    public function mount(): void
    {
        // Menggunakan Auth::guard('admin') untuk mengambil data admin
        $user = Auth::guard('admin')->user();

        $this->name = $user->name;
        // $this->username = $user->username; // Dihapus
        $this->email = $user->email;
        $this->existingPhoto = $user->profile_photo_path;
    }

    /**
     * Save the updated profile information.
     */
    public function save()
    {
        // Menggunakan Auth::guard('admin') untuk mendapatkan user yang akan diupdate
        $user = Auth::guard('admin')->user();

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            // 'username' => 'required|string|max:255|unique:users,username,' . $user->id, // Dihapus
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'photo' => 'nullable|image|max:1024', // Maksimal 1MB
        ]);

        // Update foto profil jika ada yang baru
        if ($this->photo) {
            $validated['profile_photo_path'] = $this->photo->store('profile-photos', 'public');
        }

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Hapus password dari array jika kosong agar tidak menimpa password lama
            unset($validated['password']);
        }

        // Hapus 'photo' dari array karena sudah di-handle
        unset($validated['photo']);

        $user->update($validated);
        
        session()->flash('success', 'Profil berhasil diperbarui.');

        // Redirect kembali ke halaman yang sama TANPA navigate: true
        // Ini akan memaksa refresh total pada halaman.
        return $this->redirect(route('admin.pengaturan'));
    }
}; ?>

<div>
    <!-- Header Slot -->
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan</h1>
    </x-slot>

    <!-- Main Content -->
    <main class="p-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit Profile</h2>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Profile Photo Section -->
                    <div class="md:col-span-1 flex flex-col items-center">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-40 h-40 rounded-full object-cover mb-4">
                        
                        {{-- <<< PERBAIKAN DI SINI >>> --}}
                        {{-- Menambahkan ?v={{ time() }} untuk memaksa browser memuat gambar baru --}}
                        @elseif ($existingPhoto)
                            <img src="{{ asset('storage/' . $existingPhoto) }}?v={{ time() }}" class="w-40 h-40 rounded-full object-cover mb-4">
                        
                        @else
                            <div class="w-40 h-40 rounded-full bg-gray-200 flex items-center justify-center mb-4">
                                <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        @endif
                        
                        <label for="photo" class="cursor-pointer text-sm text-blue-600 hover:text-blue-800">
                            Ganti Foto
                            <input type="file" id="photo" wire:model="photo" class="hidden">
                        </label>
                        <div wire:loading wire:target="photo" class="text-sm text-gray-500 mt-2">Uploading...</div>
                        @error('photo') <span class="text-red-500 text-xs mt-2">{{ $message }}</span> @enderror
                    </div>

                    <!-- Form Fields Section -->
                    <div class="md:col-span-2 space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Namamu</label>
                            <input type="text" id="name" wire:model="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- Field Nama Pengguna Dihapus --}}

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" wire:model="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru (opsional)</label>
                            <input type="password" id="password" wire:model="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Isi untuk mengubah password">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                <div wire:loading wire:target="save" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-3"></div>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
