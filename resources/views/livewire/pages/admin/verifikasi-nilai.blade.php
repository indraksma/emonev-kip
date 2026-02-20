<?php

use Livewire\Volt\Component;
use App\Models\Submission;
use App\Models\Penilaian;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    public Submission $submission;
    public $answers;

    // Properti untuk modal
    public $showInputModal = false;
    public $nilai;
    public $statusInformatif;

    public function mount(Submission $submission): void
    {
        $this->submission = $submission;
        $this->answers = $submission->jawaban()->with('jadwalPertanyaan')->get();
        // Load nilai yang sudah ada jika ada
        $penilaian = $this->submission->penilaian;
        if ($penilaian) {
            $this->nilai = $penilaian->nilai;
            $this->statusInformatif = $penilaian->status_informatif;
        }
    }

    public function openInputModal()
    {
        $this->showInputModal = true;
    }

    public function simpanNilai()
    {
        $this->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'statusInformatif' => 'required|in:Sangat Informatif,Cukup Informatif,Kurang Informatif',
        ]);

        Penilaian::updateOrCreate(
            ['submission_id' => $this->submission->id],
            [
                'nilai' => $this->nilai,
                'status_informatif' => $this->statusInformatif,
            ]
        );

        $this->showInputModal = false;
        session()->flash('success', 'Nilai berhasil disimpan sementara.');
    }

    public function selesaiVerifikasi(): void
    {
        // Pastikan nilai sudah diisi sebelum menyelesaikan verifikasi
        if (!$this->submission->penilaian) {
            session()->flash('error', 'Harap input nilai kuesioner terlebih dahulu sebelum menyelesaikan verifikasi.');
            return;
        }

        $this->submission->status_verifikasi = 'Terverifikasi';
        $this->submission->save();

        session()->flash('success', 'Kuesioner dari ' . $this->submission->user->badanPublik->nama_badan_publik . ' berhasil diverifikasi.');
        $this->redirectRoute('admin.penilaian', navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.penilaian') }}" wire:navigate class="p-2 rounded-full hover:bg-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Verifikasi Nilai Dinas</h1>
        </div>
    </x-slot>

    <main class="p-8">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Informasi Dinas --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nama Dinas / Badan Publik</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $submission->user->badanPublik->nama_badan_publik ?? $submission->user->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Kategori Kuesioner</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $submission->kategori->nama ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Tanggal Submit</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($submission->tanggal_submit)->isoFormat('D MMMM YYYY') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nama Responden</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $submission->user->name }}</p>
                </div>
            </div>
        </div>

        {{-- Tabel Jawaban --}}
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 flex justify-end">
                <button wire:click="openInputModal" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Input Nilai Kuesioner
                </button>
            </div>
            <div class="overflow-x-auto">
                {{-- KODE TABEL YANG DIPERBAIKI --}}
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pertanyaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pilih Jawaban</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upload Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($answers as $answer)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $answer->jadwalPertanyaan->teks_pertanyaan ?? 'Pertanyaan tidak ditemukan' }}</td>
                                <td class="px-6 py-4 text-sm font-bold {{ $answer->jawaban == 'Ya' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $answer->jawaban ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($answer->link_dokumen)
                                        <a href="{{ $answer->link_dokumen }}" target="_blank" class="text-blue-600 hover:underline">
                                            Lihat Link
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                     @if ($answer->upload_dokumen)
                                        <a href="{{ asset('storage/' . $answer->upload_dokumen) }}" target="_blank" class="text-blue-600 hover:underline">
                                            Lihat Dokumen
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada jawaban yang ditemukan untuk submission ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-50 flex justify-end space-x-3">
                <a href="{{ route('admin.penilaian') }}" wire:navigate class="px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button wire:click="selesaiVerifikasi" wire:loading.attr="disabled" class="px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400">
                    Selesai Verifikasi
                </button>
            </div>
        </div>

        {{-- MODAL UNTUK INPUT NILAI --}}
        @if($showInputModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" x-data="{ show: @entangle('showInputModal') }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6" @click.away="show = false">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-semibold">Masukkan Nilai Kuesioner</h3>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="mt-4">
                    <form wire:submit.prevent="simpanNilai">
                        <div class="space-y-4">
                            <div>
                                <label for="nilai" class="block text-sm font-medium text-gray-700">Nilai</label>
                                <input wire:model="nilai" id="nilai" type="number" placeholder="Masukkan Nilai (0-100)" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('nilai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-2 space-y-2">
                                    <label class="flex items-center">
                                        <input wire:model="statusInformatif" type="radio" value="Sangat Informatif" class="form-radio">
                                        <span class="ml-2">Sangat Informatif</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="statusInformatif" type="radio" value="Cukup Informatif" class="form-radio">
                                        <span class="ml-2">Cukup Informatif</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="statusInformatif" type="radio" value="Kurang Informatif" class="form-radio">
                                        <span class="ml-2">Kurang Informatif</span>
                                    </label>
                                </div>
                                @error('statusInformatif') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="show = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>
