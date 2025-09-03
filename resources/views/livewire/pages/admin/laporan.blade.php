<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Kategori;
use App\Models\Penilaian;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public $kategoriId = 'semua';
    public string $search = '';

    public function with(): array
    {
        $kategoris = Kategori::all();

        $query = Penilaian::query()
            ->with(['submission.user.badanPublik', 'submission.kategori']);

        // Filter berdasarkan kategori
        if ($this->kategoriId && $this->kategoriId !== 'semua') {
            $query->whereHas('submission', function ($q) {
                $q->where('kategori_id', $this->kategoriId);
            });
        }

        // Filter berdasarkan pencarian
        if (!empty($this->search)) {
            $query->whereHas('submission.user.badanPublik', function ($q) {
                $q->where('nama_badan_publik', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'penilaians' => $query->latest()->paginate(10),
            'kategoris' => $kategoris,
        ];
    }

    // Fungsi untuk mengubah warna predikat
    public function getPredikatClass($status)
    {
        return [
            'Sangat Informatif' => 'bg-green-100 text-green-800',
            'Cukup Informatif' => 'bg-yellow-100 text-yellow-800',
            'Kurang Informatif' => 'bg-red-100 text-red-800',
        ][$status] ?? 'bg-gray-100 text-gray-800';
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-8">
            <h1 class="text-3xl font-bold text-gray-900">Laporan</h1>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Badan Publik..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </x-slot>

    <main class="p-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Laporan Hasil E-Monev</h2>
                <a href="{{ route('admin.laporan.unduh', ['kategoriId' => $kategoriId]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Laporan
                </a>
            </div>

            {{-- Filter Kategori (Tabs) --}}
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                        <button wire:click="$set('kategoriId', 'semua')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $kategoriId === 'semua' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Semua Kategori
                        </button>
                        @foreach ($kategoris as $kategori)
                            <button wire:click="$set('kategoriId', {{ $kategori->id }})" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $kategoriId == $kategori->id ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                {{ $kategori->nama }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badan Publik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori Kuesioner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Submit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($penilaians as $penilaian)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $loop->iteration + $penilaians->firstItem() - 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $penilaian->submission->user->badanPublik->nama_badan_publik ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $penilaian->submission->kategori->nama ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $penilaian->nilai }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($penilaian->submission->tanggal_submit)->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $this->getPredikatClass($penilaian->status_informatif) }}">
                                        {{ $penilaian->status_informatif }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data laporan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $penilaians->links() }}
            </div>
        </div>
    </main>
</div>
