<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Kategori;
use App\Models\Pertanyaan;

new #[Layout('components.layouts.admin')] class extends Component
{
    public ?Pertanyaan $pertanyaan;
    public Kategori $kategori;

    public string $teks_pertanyaan = '';
    public string $tipe_jawaban = 'Ya/Tidak';
    public bool $butuh_link = false;
    public bool $butuh_upload = false;

    /**
     * Mount the component and handle both create and edit modes.
     */
    public function mount(Pertanyaan $pertanyaan, Kategori $kategori): void
    {
        $this->pertanyaan = $pertanyaan;
        $this->kategori = $kategori;

        if ($this->pertanyaan->exists) {
            // Mode Edit: Ambil kategori dari relasi pertanyaan
            $this->teks_pertanyaan = $this->pertanyaan->teks_pertanyaan;
            $this->butuh_link = $this->pertanyaan->butuh_link;
            $this->butuh_upload = $this->pertanyaan->butuh_upload;
        } else {
            // Mode Tambah: Ambil kategori dari parameter
            $this->kategori = $kategori;
        }
    }

    /**
     * Save (create or update) the question.
     */
    public function save()
    {
        $validated = $this->validate([
            'teks_pertanyaan' => 'required|string|min:10',
            'butuh_link' => 'required|boolean',
            'butuh_upload' => 'required|boolean',
        ]);

        $dataToSave = [
            'teks_pertanyaan' => $validated['teks_pertanyaan'],
            'tipe_jawaban' => $this->tipe_jawaban,
            'butuh_link' => $validated['butuh_link'],
            'butuh_upload' => $validated['butuh_upload'],
        ];

        if ($this->pertanyaan->exists) {
            // Mode Edit: Update data
            $this->pertanyaan->update($dataToSave);
            session()->flash('success', 'Pertanyaan berhasil diperbarui.');
        } else {
            // Mode Tambah: Buat data baru
            $this->kategori->pertanyaans()->create($dataToSave);
            session()->flash('success', 'Pertanyaan baru berhasil ditambahkan.');
        }

        return $this->redirectRoute('admin.kuesioner.pertanyaan.index', ['kategori' => $this->kategori->id]);
    }
}; ?>

<div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="bg-white p-6 rounded-lg shadow-md">

            <!-- Header Form (Dinamis) -->
            <div class="flex items-center mb-6">
                <a href="{{ route('admin.kuesioner.pertanyaan.index', ['kategori' => $kategori->id]) }}" wire:navigate class="text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-800 ml-4">
                    {{ $pertanyaan->exists ? 'Edit Pertanyaan' : 'Buat Pertanyaan' }}
                </h1>
            </div>
            
            <!-- Form -->
            <form wire:submit="save">
                <div class="space-y-6">
                    
                    <!-- Pertanyaan -->
                    <div>
                        <label for="teks_pertanyaan" class="block text-sm font-medium text-gray-700">Pertanyaan</label>
                        <textarea id="teks_pertanyaan" wire:model="teks_pertanyaan" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Masukkan Pertanyaan"></textarea>
                        @error('teks_pertanyaan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tipe Jawaban -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipe Jawaban</label>
                        <div class="mt-2 text-sm text-gray-500">
                            Ya / Tidak
                        </div>
                    </div>

                    <!-- Persyaratan Bukti -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Persyaratan Bukti</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input id="butuh_link" wire:model="butuh_link" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="butuh_link" class="ml-2 block text-sm text-gray-900">Link Dokumen</label>
                            </div>
                            <div class="flex items-center">
                                <input id="butuh_upload" wire:model="butuh_upload" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="butuh_upload" class="ml-2 block text-sm text-gray-900">Upload File Dokumen</label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Aksi (Dinamis) -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.kuesioner.pertanyaan.index', ['kategori' => $kategori->id]) }}" wire:navigate
                        class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        {{ $pertanyaan->exists ? 'Simpan Perubahan' : 'Simpan' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
