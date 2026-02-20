<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Kategori;
use Livewire\Attributes\Layout;
use App\Models\Submission;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public $kategoriId;
    public $search = ''; // Properti untuk menampung input pencarian

    public function filterByKategori($kategoriId)
    {
        $this->kategoriId = $kategoriId;
        $this->resetPage();
    }

    public function with()
    {
        $kategoris = Kategori::all();

        // Query dasar untuk mengambil data submission
        $query = Submission::with(['user.badanPublik', 'kategori'])
                        ->latest('tanggal_submit');

        // Terapkan filter kategori jika ada yang dipilih
        if ($this->kategoriId && $this->kategoriId !== 'semua') {
            $query->where('kategori_id', $this->kategoriId);
        }

        // Terapkan filter pencarian
        if (!empty($this->search)) {
            $query->whereHas('user.badanPublik', function ($q) {
                $q->where('nama_badan_publik', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'submissions' => $query->paginate(10),
            'kategoris' => $kategoris,
        ];
    }
}; ?>

<div>
    {{-- Slot untuk header halaman --}}
    <x-slot name="header">
        <div class="flex items-center space-x-8">
            <h1 class="text-3xl font-bold text-gray-900">Penilaian</h1>
        </div>
    </x-slot>

    {{-- Konten utama halaman --}}
    <main class="p-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">List Verifikasi Nilai Dinas</h2>

            {{-- Filter Kategori (Tabs) --}}
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                        <a href="#" wire:click.prevent="filterByKategori('semua')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer {{ !$kategoriId || $kategoriId === 'semua' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Semua Kategori
                        </a>
                        @foreach ($kategoris as $kategori)
                            <a href="#" wire:click.prevent="filterByKategori({{ $kategori->id }})"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer {{ $kategoriId == $kategori->id ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                {{ $kategori->nama }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PPID Pelaksana</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Submit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($submissions as $submission)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration + $submissions->firstItem() - 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $submission->user->badanPublik->nama_badan_publik ?? $submission->user->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $submission->kategori->nama ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($submission->tanggal_submit)->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $submission->status_verifikasi == 'Terverifikasi' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $submission->status_verifikasi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <a href="{{ route('admin.verifikasi.show', $submission->id) }}" class="px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                                        Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $submissions->links() }}
            </div>
        </div>
    </main>
</div>
