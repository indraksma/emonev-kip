<?php

use App\Models\User;
use App\Models\Submission;
use App\Models\Laporan;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    public int $ppidTerdaftar = 0;
    public int $menungguVerifikasi = 0;
    public int $selesaiVerifikasi = 0;
    public int $telahDinilai = 0;

    public $listVerifikasi;

    public function mount(): void
    {
        $this->ppidTerdaftar = User::where('role', 'dinas')->count();

        $this->menungguVerifikasi = Submission::where('status_verifikasi', 'Menunggu')->count();
        $this->selesaiVerifikasi = Submission::where('status_verifikasi', 'Terverifikasi')->count();

        $this->telahDinilai = Laporan::count();

        $this->listVerifikasi = Submission::with(['user.badanPublik', 'kategori']) // Relasi 'kategori' ada di Submission
                                    ->latest('tanggal_submit')
                                    ->take(10)
                                    ->get();
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        </div>
    </x-slot>

    <main class="p-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Card 1 --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">PPID Pelaksana Terdaftar</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $ppidTerdaftar }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            {{-- Card 2 --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kuesioner Menunggu Verifikasi</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $menungguVerifikasi }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            {{-- Card 3 --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kuesioner Selesai Di Verifikasi</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $selesaiVerifikasi }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            {{-- Card 4 --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">PPID Pelaksana Telah Dinilai</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $telahDinilai }}</p>
                </div>
                <div class="bg-pink-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- List Verifikasi Nilai -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">List Verifikasi Nilai Dinas</h2>
                <a href="{{ route('admin.penilaian') }}" wire:navigate class="text-sm font-medium text-blue-600 hover:underline">Lihat Semua List &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PPID Pelaksana</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori Kuesioner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Submit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Verifikasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($listVerifikasi as $index => $item)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm">{{ $item->user->badanPublik->nama_badan_publik ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">

                                        {{ $item->kategori->nama ?? 'N/A' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($item->tanggal_submit)->format('d F Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($item->status_verifikasi == 'Terverifikasi')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.verifikasi.show', $item->id) }}" wire:navigate class="px-3 py-1 text-xs font-medium text-white bg-gray-800 rounded-md hover:bg-gray-900">Verifikasi</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada data untuk diverifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
