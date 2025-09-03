<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\BadanPublik;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    public User $user;

    // Properti untuk form binding
    public $nama_badan_publik;
    public $website;
    public $telepon_badan_publik;
    public $email_badan_publik;
    public $alamat;
    public $nama_responden;
    public $telepon_responden;
    public $jabatan;
    public $email;
    public $password;

    public function mount(User $user): void
    {
        $this->user = $user->load('badanPublik');
        
        // Isi properti form dengan data yang ada
        $this->nama_badan_publik = $this->user->badanPublik->nama_badan_publik;
        $this->website = $this->user->badanPublik->website;
        $this->telepon_badan_publik = $this->user->badanPublik->telepon_badan_publik;
        $this->email_badan_publik = $this->user->badanPublik->email_badan_publik;
        $this->alamat = $this->user->badanPublik->alamat;
        $this->nama_responden = $this->user->name;
        $this->telepon_responden = $this->user->badanPublik->telepon_responden;
        $this->jabatan = $this->user->badanPublik->jabatan;
        $this->email = $this->user->email;
    }

    public function save()
    {
        $validated = $this->validate([
            'nama_badan_publik' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'telepon_badan_publik' => 'required|string|max:20',
            'email_badan_publik' => 'required|email|max:255',
            'alamat' => 'required|string',
            'nama_responden' => 'required|string|max:255',
            'telepon_responden' => 'required|string|max:20',
            'jabatan' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password' => 'nullable|string|min:8',
        ]);

        // Update data di tabel users
        $this->user->name = $this->nama_responden;
        $this->user->email = $this->email;
        if (!empty($this->password)) {
            $this->user->password = Hash::make($this->password);
        }
        $this->user->save();

        // Update data di tabel badan_publiks
        $this->user->badanPublik->update([
            'nama_badan_publik' => $this->nama_badan_publik,
            'website' => $this->website,
            'telepon_badan_publik' => $this->telepon_badan_publik,
            'email_badan_publik' => $this->email_badan_publik,
            'alamat' => $this->alamat,
            'telepon_responden' => $this->telepon_responden,
            'jabatan' => $this->jabatan,
        ]);

        session()->flash('success', 'Data Badan Publik berhasil diperbarui.');
        $this->redirectRoute('admin.badan-publik.detail', ['user' => $this->user->id], navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.badan-publik.detail', ['user' => $user->id]) }}" wire:navigate class="p-2 rounded-full hover:bg-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Detail Badan Publik</h1>
        </div>
    </x-slot>

    <main class="p-8">
        <form wire:submit="save">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Edit Informasi: {{ $user->badanPublik->nama_badan_publik ?? 'N/A' }}
                    </h2>
                </div>

                <div class="mt-6 space-y-8">
                    {{-- Informasi Umum & Kontak --}}
                    <fieldset>
                        <legend class="text-base font-semibold text-gray-900 mb-2">Informasi Umum & Kontak</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_badan_publik" class="block text-sm font-medium text-gray-700">Nama Badan Publik</label>
                                <input wire:model="nama_badan_publik" id="nama_badan_publik" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('nama_badan_publik') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                <input wire:model="website" id="website" type="url" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('website') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="telepon_badan_publik" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input wire:model="telepon_badan_publik" id="telepon_badan_publik" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('telepon_badan_publik') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email_badan_publik" class="block text-sm font-medium text-gray-700">Email</label>
                                <input wire:model="email_badan_publik" id="email_badan_publik" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email_badan_publik') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <input wire:model="alamat" id="alamat" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('alamat') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Informasi Responden Utama --}}
                    <fieldset>
                        <legend class="text-base font-semibold text-gray-900 mb-2">Informasi Responden Utama</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_responden" class="block text-sm font-medium text-gray-700">Nama Responden</label>
                                <input wire:model="nama_responden" id="nama_responden" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('nama_responden') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="telepon_responden" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input wire:model="telepon_responden" id="telepon_responden" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('telepon_responden') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input wire:model="jabatan" id="jabatan" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('jabatan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email_responden" class="block text-sm font-medium text-gray-700">Email Responden (untuk login)</label>
                                <input wire:model="email" id="email_responden" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Informasi Akun E-Monev --}}
                    <fieldset>
                        <legend class="text-base font-semibold text-gray-900 mb-2">Informasi Akun E-Monev</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email_akun" class="block text-sm font-medium text-gray-700">Email</label>
                                <input wire:model="email" id="email_akun" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru (opsional)</label>
                                <div class="mt-1 relative">
                                    <input wire:model="password" id="password" type="password" placeholder="Isi untuk mengubah password"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10">
                                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                                        <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m0 11.452A9.96 9.96 0 0112 17c-4.478 0-8.268-2.943-9.542-7a10.034 10.034 0 013.454-4.545m1.546-1.546A10.008 10.008 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-.68 2.455m-1.455 1.455A10.05 10.05 0 0112 19c-1.654 0-3.21-.48-4.545-1.31M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('admin.badan-publik.detail', ['user' => $user->id]) }}" wire:navigate class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

{{-- DIUBAH: Memindahkan script langsung ke dalam file --}}
<script>
    document.addEventListener('livewire:navigated', () => {
        // Fungsi ini akan dijalankan setiap kali Livewire selesai navigasi
        // Ini memastikan fungsi toggle selalu tersedia
        window.togglePasswordVisibility = function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (!passwordInput) return; // Exit jika elemen tidak ditemukan

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    });
</script>
