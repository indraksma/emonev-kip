<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Kategori;

new #[Layout('components.layouts.admin')] class extends Component
{

    public ?Kategori $kategori;

    public $nama;
    public $judul;
    public $deskripsi;

    protected function rules()
    {
        return [
            'nama'      => 'required|string|min:3|max:255',
            'judul'     => 'required|string|min:5|max:255',
            'deskripsi' => 'required|string|max:1000',
        ];
    }

    public function mount(Kategori $kategori): void
    {
        $this->kategori = $kategori;
        if ($this->kategori->exists) {
            $this->nama = $this->kategori->nama;
            $this->judul = $this->kategori->judul;
            $this->deskripsi = $this->kategori->deskripsi;
        }
    }

    public function save()
    {
        try {
            $validatedData = $this->validate();

            if ($this->kategori->exists) {
                $this->kategori->update($validatedData);
                session()->flash('success', 'Kategori berhasil diperbarui.');
            } else {
                Kategori::create($validatedData);
                session()->flash('success', 'Kategori baru berhasil ditambahkan.');
            }

            return $this->redirectRoute('admin.kuesioner', navigate: true);

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error saving kategori: ' . $e->getMessage());

            // Tampilkan pesan error ke user
            $this->addError('form', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }
}; ?>

<div>
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @error('form')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Mohon periksa kembali input Anda.</span>
        </div>
    @endif

    <div class="p-4 sm:p-6 lg:p-8">
        <div class="bg-white p-6 rounded-lg shadow-md">

            <!-- Header Form (Dinamis) -->
            <div class="flex items-center mb-6">
                <a href="{{ route('admin.kuesioner') }}" wire:navigate class="text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-800 ml-4">
                    {{ $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori' }}
                </h1>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save">
                <div class="space-y-6">

                    <!-- Nama Kategori -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" id="nama" wire:model="nama"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Masukkan nama kategori">
                        @error('nama') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Judul Kategori -->
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Kategori</label>
                        <input type="text" id="judul" wire:model="judul"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Masukkan judul kategori">
                        @error('judul') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Deskripsi Kategori -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Kategori</label>
                        <textarea id="deskripsi" wire:model="deskripsi" rows="4"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Masukkan deskripsi kategori"></textarea>
                        @error('deskripsi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                </div>

                <!-- Tombol Aksi (Dinamis) -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.kuesioner') }}" wire:navigate
                    class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove>{{ $kategori->exists ? 'Simpan Perubahan' : 'Simpan' }}</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
