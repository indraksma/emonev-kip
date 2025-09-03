<?php

use App\Models\Kategori;
use App\Models\Jadwal; 
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Livewire\Attributes\On;

new #[Layout('components.layouts.admin')] class extends Component
{
    public $kategoriList;
    public $activeKategoriId;
    public $activeKategori;
    public $jadwal; 

    /**
     * Mount the component and load data.
     */
    public function mount(): void
    {
        $this->loadJadwal();
        $this->loadKategori();
    }

    /**
     * Load the schedule.
     */
    public function loadJadwal(): void
    {
        $this->jadwal = Jadwal::first();
    }

    /**
     * Load all categories and set the first one as active.
     */
    public function loadKategori(): void
    {
        $this->kategoriList = Kategori::orderBy('id')->get();
        
        if ($this->kategoriList->isNotEmpty()) {
            if (!$this->activeKategoriId || !$this->kategoriList->contains('id', $this->activeKategoriId)) {
                $this->activeKategoriId = $this->kategoriList->first()->id;
            }
        } else {
            $this->activeKategoriId = null;
        }
        $this->updateActiveKategori();
    }

    /**
     * Update the active category property.
     */
    public function updateActiveKategori(): void
    {
        $this->activeKategori = Kategori::find($this->activeKategoriId);
    }

    /**
     * Change the active category.
     */
    public function changeKategori($kategoriId): void
    {
        $this->activeKategoriId = $kategoriId;
        $this->updateActiveKategori();
    }

    /**
     * Delete the currently active category.
     */
    public function deleteKategori(): void
    {
        if ($this->activeKategori) {
            if ($this->activeKategori->gambar) {
                Storage::disk('public')->delete($this->activeKategori->gambar);
            }
            
            $this->activeKategori->delete();
            session()->flash('success', 'Kategori berhasil dihapus.');
            $this->loadKategori();
        }
    }
}; ?>

<div>
    <!-- Header Slot -->
    <x-slot name="header">
        <div class="flex items-center space-x-8">
            <h1 class="text-3xl font-bold text-gray-900">Kuesioner</h1>
            <div class="relative">
                <input type="text" placeholder="Cari sesuatu..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </x-slot>

    <!-- Main Content -->
    <main class="p-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Kategori Kuesioner</h2>
                <div class="flex space-x-2">
                    {{-- Tombol Jadwal Kuesioner (Diperbarui) --}}
                    <a href="{{ route('admin.kuesioner.jadwal') }}" wire:navigate class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">Jadwal Kuesioner</a>
                    
                    @if($activeKategori)
                        <button 
                            wire:click="deleteKategori" 
                            wire:confirm="Apakah Anda yakin ingin menghapus kategori ini?"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Kategori
                        </button>
                    @endif

                    <a href="{{ route('admin.kuesioner.kategori.create') }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Kategori
                    </a>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                    @forelse($kategoriList as $kategori)
                        <button wire:click="changeKategori({{ $kategori->id }})"
                                class="{{ $activeKategoriId == $kategori->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} whitespace-nowrap py-2 px-5 rounded-t-md font-medium text-sm transition">
                            {{ $kategori->nama }}
                        </button>
                    @empty
                    @endforelse
                </nav>
            </div>

            <div class="mt-8">
                @if($activeKategori)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <img src="{{ $activeKategori->gambar ? asset('storage/' . $activeKategori->gambar) : 'https://placehold.co/800x200/EBF4FF/7F9CF5?text=Gambar+Kategori' }}" alt="Category Image" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900">{{ $activeKategori->judul }}</h3>
                            
                            {{-- Menampilkan Jadwal --}}
                            @if($jadwal)
                                <div class="mt-2 text-xs text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>
                                        Jadwal: {{ Carbon::parse($jadwal->tanggal_mulai)->isoFormat('D MMMM YYYY') }} - {{ Carbon::parse($jadwal->tanggal_selesai)->isoFormat('D MMMM YYYY') }}
                                    </span>
                                </div>
                            @endif

                            <p class="mt-3 text-sm text-gray-600">
                                {{ $activeKategori->deskripsi }}
                            </p>
                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="{{ route('admin.kuesioner.kategori.edit', ['kategori' => $activeKategori->id]) }}" wire:navigate class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Kategori
                                </a>
                                <a href="{{ route('admin.kuesioner.pertanyaan.index', ['kategori' => $activeKategori->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Detail Pertanyaan
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500">Belum ada kategori yang dibuat.</p>
                        <p class="text-sm text-gray-500 mt-2">Silakan klik "Tambah Kategori" untuk memulai.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
