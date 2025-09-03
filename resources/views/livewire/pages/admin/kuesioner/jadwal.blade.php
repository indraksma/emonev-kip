<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Jadwal;

new #[Layout('components.layouts.admin')] class extends Component
{
    public $tanggal_mulai;
    public $tanggal_selesai;

    /**
     * Mount the component and load the existing schedule.
     */
    public function mount(): void
    {
        $jadwal = Jadwal::first();

        if ($jadwal) {
            $this->tanggal_mulai = $jadwal->tanggal_mulai;
            $this->tanggal_selesai = $jadwal->tanggal_selesai;
        }
    }

    /**
     * Save or update the schedule.
     */
    public function save()
    {
        $this->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        Jadwal::updateOrCreate(
            ['id' => 1],
            [
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
            ]
        );

        session()->flash('success', 'Jadwal kuesioner berhasil disimpan.');

        $this->dispatch('jadwalUpdated'); 

        return $this->redirectRoute('admin.kuesioner', navigate: true);
    }

    /**
     * PERBAIKAN: Fungsi baru untuk mereset/menghapus jadwal.
     */
    public function resetJadwal()
    {
        $jadwal = Jadwal::first();
        if ($jadwal) {
            $jadwal->delete();
            session()->flash('success', 'Jadwal kuesioner berhasil direset.');
        } else {
            // Opsional: pesan jika tidak ada jadwal untuk direset
            session()->flash('info', 'Tidak ada jadwal yang aktif untuk direset.');
        }

        $this->dispatch('jadwalUpdated');

        // Kembali ke halaman kuesioner utama
        return $this->redirectRoute('admin.kuesioner', navigate: true);
    }
}; ?>

<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="bg-white p-6 rounded-lg shadow-md">

            <!-- Header Form -->
            <div class="flex items-center mb-6">
                <a href="{{ route('admin.kuesioner') }}" wire:navigate class="text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-800 ml-4">Jadwal Kuesioner</h1>

                <!-- PERBAIKAN: Tombol Reset Jadwal di pojok kanan -->
                <div class="ml-auto">
                    <button 
                        wire:click="resetJadwal" 
                        wire:confirm="Anda yakin ingin mereset dan menghapus jadwal kuesioner?"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 inline-flex items-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Reset Jadwal
                    </button>
                </div>
            </div>
            
            <!-- Form -->
            <form wire:submit="save">
                <div class="space-y-6">
                    
                    <!-- Tanggal Mulai -->
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Tanggal Mulai</span>
                        <div
                            class="relative mt-1 flex items-center w-full border border-gray-300 rounded-md shadow-sm focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500"
                        >
                            <div class="pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <input
                                type="date"
                                wire:model="tanggal_mulai"
                                onfocus="this.showPicker()"
                                class="block w-full pl-2 pr-3 py-2 border-0 rounded-md focus:ring-0 sm:text-sm bg-transparent cursor-pointer"
                            >
                        </div>
                        @error('tanggal_mulai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Tanggal Selesai</span>
                        <div
                            class="relative mt-1 flex items-center w-full border border-gray-300 rounded-md shadow-sm focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500"
                        >
                            <div class="pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <input
                                type="date"
                                wire:model="tanggal_selesai"
                                onfocus="this.showPicker()"
                                class="block w-full pl-2 pr-3 py-2 border-0 rounded-md focus:ring-0 sm:text-sm bg-transparent cursor-pointer"
                            >
                        </div>
                        @error('tanggal_selesai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                </div>

                <!-- Tombol Aksi -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.kuesioner') }}" wire:navigate
                        class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>