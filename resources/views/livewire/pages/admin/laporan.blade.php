<?php

use App\Models\BadanPublik;
use App\Models\HasilPenilaian;
use App\Models\Jadwal;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public $jadwalId = 'semua';
    public string $search = '';

    public function with(): array
    {
        $jadwals = Jadwal::query()->orderBy('tahun', 'desc')->get();

        // Get all unique user IDs that have verified results
        $userIdsWithResults = HasilPenilaian::query()
            ->where('status_verifikasi', 'Terverifikasi')
            ->distinct()
            ->pluck('user_id');

        $query = BadanPublik::query()
            ->with(['user' => function ($q) {
                $q->with(['hasilPenilaians' => function ($hq) {
                    $hq->where('status_verifikasi', 'Terverifikasi')
                        ->with('jadwal', 'klasifikasiPenilaian');
                }]);
            }])
            ->whereHas('user', function ($q) {
                // Get all users that have badan publik
            });

        if (!empty($this->search)) {
            $query->where('nama_badan_publik', 'like', '%' . $this->search . '%');
        }

        $badanPubliks = $query->orderBy('nama_badan_publik')->paginate(10);

        return [
            'badanPubliks' => $badanPubliks,
            'jadwals' => $jadwals,
        ];
    }

    public function getHasilPenilaian($userId, $jadwalId)
    {
        $query = HasilPenilaian::query()
            ->with('jadwal', 'klasifikasiPenilaian')
            ->where('user_id', $userId)
            ->where('status_verifikasi', 'Terverifikasi');

        if ($this->jadwalId && $this->jadwalId !== 'semua') {
            $query->where('jadwal_id', $this->jadwalId);
        }

        return $query->first();
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
                <h2 class="text-lg font-semibold text-gray-800">Laporan Hasil E-Monev KIP</h2>
            </div>

            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                    <button wire:click="$set('jadwalId', 'semua')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $jadwalId === 'semua' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">Semua Jadwal</button>
                    @foreach ($jadwals as $jadwal)
                        <button wire:click="$set('jadwalId', {{ $jadwal->id }})" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $jadwalId == $jadwal->id ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ $jadwal->nama }} ({{ $jadwal->tahun }})
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badan Publik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klasifikasi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($badanPubliks as $badanPublik)
                            @php
                                $hasil = $this->getHasilPenilaian($badanPublik->user_id, $jadwalId);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $loop->iteration + $badanPubliks->firstItem() - 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $badanPublik->nama_badan_publik ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $hasil?->jadwal->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $hasil ? number_format($hasil->nilai_akhir, 2) : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $hasil?->klasifikasiPenilaian->nama ?? 'Belum terklasifikasi' }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if ($hasil)
                                        <a href="{{ route('admin.laporan.unduh.per-badan-publik', ['userId' => $badanPublik->user_id, 'jadwalId' => ($jadwalId === 'semua' ? $hasil->jadwal_id : $jadwalId)]) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Unduh
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-xs font-medium rounded cursor-not-allowed">
                                            Belum Ada Laporan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data badan publik yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $badanPubliks->links() }}</div>
        </div>
    </main>
</div>
