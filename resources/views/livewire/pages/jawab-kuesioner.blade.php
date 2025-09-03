<?php

use App\Models\Kategori;
use App\Models\Jawaban;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public $kategoriList;
    public $activeKategoriId;
    public $pertanyaanList = [];

    public $jawaban = [];
    public $link_dokumen = [];
    public $upload_dokumen = [];

    /**
     * Mount the component, load categories and questions.
     */
    public function mount(): void
    {
        $this->kategoriList = Kategori::orderBy('id')->get();
        if ($this->kategoriList->isNotEmpty()) {
            $this->changeKategori($this->kategoriList->first()->id);
        }
    }

    /**
     * Change the active category and load its questions.
     */
    public function changeKategori($kategoriId): void
    {
        $this->activeKategoriId = $kategoriId;
        $kategori = Kategori::find($kategoriId);
        $this->pertanyaanList = $kategori ? $kategori->pertanyaans : []; // Pastikan selalu array
    }

    /**
     * Save the answers and automatically move to the next category or finish.
     */
    public function simpanJawaban(): void
    {
        // Validasi input (tidak berubah)
        $this->validate([
            'jawaban.*' => ['nullable', 'in:Ya,Tidak'],
            'link_dokumen.*' => ['nullable', 'url'],
            'upload_dokumen.*' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
        ]);

        $submission = \App\Models\Submission::create([
            'user_id' => Auth::id(),
            'kategori_id' => $this->activeKategoriId,
            'tanggal_submit' => now(),
        ]);

        // Proses penyimpanan jawaban ke database (tidak berubah)
        foreach ($this->pertanyaanList as $pertanyaan) {
            $jawabanData = [
                'submission_id' => $submission->id, 
                'pertanyaan_id' => $pertanyaan->id,
                'jawaban'       => $this->jawaban[$pertanyaan->id] ?? null,
                'link_dokumen'  => $this->link_dokumen[$pertanyaan->id] ?? null,
            ];

            if (isset($this->upload_dokumen[$pertanyaan->id])) {
                $path = $this->upload_dokumen[$pertanyaan->id]->store('dokumen_kuesioner', 'public');
                $jawabanData['upload_dokumen'] = $path;
            }

            \App\Models\Jawaban::updateOrCreate(
                [
                    'submission_id' => $submission->id, 
                    'pertanyaan_id' => $pertanyaan->id,
                ],
                $jawabanData
            );
        }

        // --- Logika Baru untuk Perpindahan Otomatis ---

        // 1. Cari indeks dari kategori yang sedang aktif
        $currentKategoriIndex = $this->kategoriList->search(function ($kategori) {
            return $kategori->id == $this->activeKategoriId;
        });

        // 2. Cek apakah ada kategori berikutnya
        if ($currentKategoriIndex !== false && isset($this->kategoriList[$currentKategoriIndex + 1])) {
            // Jika ADA, pindah ke kategori berikutnya
            $nextKategori = $this->kategoriList[$currentKategoriIndex + 1];
            $this->changeKategori($nextKategori->id);

            // Tampilkan pesan sukses untuk kategori yang baru saja disimpan
            session()->flash('status', 'Jawaban berhasil disimpan! Lanjut ke kategori berikutnya.');

        } else {
            // Jika ini adalah kategori TERAKHIR, beri pesan dan redirect
            session()->flash('status', 'Selamat! Anda telah menyelesaikan seluruh kuesioner.');
            $this->redirectRoute('kuesioner'); // Mengarah ke halaman menu kuesioner
        }
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect('/', navigate: true);
    }
}; ?>


    <div class="min-h-screen bg-gray-100">
        <main class="py-12">
            <div class="max-w-screen-xl mx-auto px-6 md:px-20">
                @if (session('status'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif

                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="mb-8">
                        <nav class="flex space-x-4 overflow-x-auto" aria-label="Tabs">
                            @forelse($kategoriList as $kategori)
                                <button wire:click="changeKategori({{ $kategori->id }})"
                                        class="{{ $activeKategoriId == $kategori->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} whitespace-nowrap py-2 px-5 rounded-md font-medium text-sm transition">
                                    {{ $kategori->nama }}
                                </button>
                            @empty
                                <p class="text-sm text-gray-500">Kategori belum tersedia. Silakan hubungi admin.</p>
                            @endforelse
                        </nav>
                    </div>

                    <div class="mt-8">
                        <form wire:submit="simpanJawaban">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pilih Jawaban</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link Dokumen</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload Dokumen</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($pertanyaanList as $index => $pertanyaan)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-900">{{ $pertanyaan->teks_pertanyaan }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center space-x-4">
                                                        <label class="flex items-center">
                                                            <input wire:model="jawaban.{{ $pertanyaan->id }}" type="radio" value="Ya" class="form-radio h-4 w-4 text-blue-600">
                                                            <span class="ml-2 text-sm text-gray-700">Ya</span>
                                                        </label>
                                                        <label class="flex items-center">
                                                            <input wire:model="jawaban.{{ $pertanyaan->id }}" type="radio" value="Tidak" class="form-radio h-4 w-4 text-blue-600">
                                                            <span class="ml-2 text-sm text-gray-700">Tidak</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input wire:model="link_dokumen.{{ $pertanyaan->id }}" type="url" placeholder="Masukkan link..." class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input wire:model="upload_dokumen.{{ $pertanyaan->id }}" type="file" class="text-sm">
                                                    <p class="text-xs text-gray-500 mt-1">PDF (maks 2MB)</p>
                                                    <x-input-error :messages="$errors->get('upload_dokumen.' . $pertanyaan->id)" class="mt-1" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    Belum ada pertanyaan untuk kategori ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                                    Simpan Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
