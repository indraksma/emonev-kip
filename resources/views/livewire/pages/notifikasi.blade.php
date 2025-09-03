<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesan;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component
{
    public $pesans;
    public $pesanAktif;
    public $filter = 'semua'; // 'semua', 'belum', 'sudah'

    public function mount()
    {
        $this->loadPesans();
    }

    public function loadPesans()
    {
        $query = Auth::user()->pesans();

        if ($this->filter === 'belum') {
            $query->wherePivotNull('dibaca_pada');
        } elseif ($this->filter === 'sudah') {
            $query->wherePivotNotNull('dibaca_pada');
        }

        $this->pesans = $query->get();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->loadPesans();
        $this->pesanAktif = null; // Reset pesan aktif saat filter berubah
    }

    public function lihatPesan(Pesan $pesan)
    {
        $this->pesanAktif = $pesan;

        // Tandai sebagai sudah dibaca jika belum
        Auth::user()->pesans()->updateExistingPivot($pesan->id, [
            'dibaca_pada' => now()
        ]);

        // Muat ulang daftar pesan untuk update status
        $this->loadPesans();
    }
}; ?>

<div>
    <main class="py-12">
        <div class="max-w-screen-xl mx-auto px-6 md:px-20">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Notifikasi</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Kolom Kiri: Daftar Pesan --}}
                <div class="md:col-span-1 bg-white p-4 rounded-lg shadow-md h-fit">
                    <h2 class="font-semibold mb-2">Kotak Masuk</h2>
                    <input type="text" placeholder="Cari Pesan" class="w-full border-gray-300 rounded-md text-sm mb-4">
                    
                    <div class="flex border-b mb-2">
                        <button wire:click="setFilter('semua')" class="px-4 py-2 text-sm {{ $filter == 'semua' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500' }}">Semua</button>
                        <button wire:click="setFilter('belum')" class="px-4 py-2 text-sm {{ $filter == 'belum' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500' }}">Belum Dibaca</button>
                        <button wire:click="setFilter('sudah')" class="px-4 py-2 text-sm {{ $filter == 'sudah' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500' }}">Sudah Dibaca</button>
                    </div>

                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @forelse($pesans as $pesan)
                            <div wire:click="lihatPesan({{ $pesan->id }})" 
                                 class="p-3 rounded-md cursor-pointer {{ $pesanAktif && $pesanAktif->id == $pesan->id ? 'bg-blue-100' : 'hover:bg-gray-50' }}">
                                <div class="flex justify-between items-start">
                                    <p class="font-semibold text-gray-800">{{ $pesan->judul }}</p>
                                    @if(!$pesan->pivot->dibaca_pada)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pesan->created_at)->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="p-3 text-sm text-gray-500">Tidak ada pesan.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom Kanan: Isi Pesan --}}
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    @if($pesanAktif)
                        <div class="border-b pb-4 mb-4">
                            <h2 class="text-xl font-bold text-gray-900">{{ $pesanAktif->judul }}</h2>
                            <p class="text-sm text-gray-500">Dikirim pada: {{ \Carbon\Carbon::parse($pesanAktif->created_at)->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                        </div>
                        <div class="prose max-w-none">
                            {!! nl2br(e($pesanAktif->isi)) !!}
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500">Pilih pesan untuk dibaca.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
