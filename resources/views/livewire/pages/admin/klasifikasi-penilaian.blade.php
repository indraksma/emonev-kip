<?php

use App\Models\HasilPenilaian;
use App\Models\KlasifikasiPenilaian;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    public ?int $editingId = null;
    public string $nama = '';
    public $minNilai = '';
    public $maxNilai = '';
    public $urutan = 0;
    public bool $isActive = true;

    public function with(): array
    {
        return [
            'items' => KlasifikasiPenilaian::query()->orderBy('urutan')->get(),
        ];
    }

    public function edit(int $id): void
    {
        $item = KlasifikasiPenilaian::findOrFail($id);
        $this->editingId = $item->id;
        $this->nama = $item->nama;
        $this->minNilai = (string) $item->min_nilai;
        $this->maxNilai = (string) $item->max_nilai;
        $this->urutan = $item->urutan;
        $this->isActive = (bool) $item->is_active;
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'nama', 'minNilai', 'maxNilai', 'urutan']);
        $this->isActive = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nama' => 'required|string|max:255',
            'minNilai' => 'required|numeric|min:0|max:100',
            'maxNilai' => 'required|numeric|min:0|max:100|gte:minNilai',
            'urutan' => 'required|integer|min:0',
            'isActive' => 'required|boolean',
        ], [
            'maxNilai.gte' => 'Nilai maksimal harus lebih besar atau sama dengan nilai minimal.',
        ]);

        $overlapQuery = KlasifikasiPenilaian::query()
            ->where('is_active', true)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('min_nilai', [$validated['minNilai'], $validated['maxNilai']])
                    ->orWhereBetween('max_nilai', [$validated['minNilai'], $validated['maxNilai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('min_nilai', '<=', $validated['minNilai'])
                          ->where('max_nilai', '>=', $validated['maxNilai']);
                    });
            });

        if ($this->editingId) {
            $overlapQuery->where('id', '!=', $this->editingId);
        }

        if ($validated['isActive'] && $overlapQuery->exists()) {
            $this->addError('minNilai', 'Rentang nilai overlap dengan klasifikasi aktif lain.');
            return;
        }

        KlasifikasiPenilaian::query()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'nama' => $validated['nama'],
                'min_nilai' => $validated['minNilai'],
                'max_nilai' => $validated['maxNilai'],
                'urutan' => $validated['urutan'],
                'is_active' => $validated['isActive'],
            ]
        );

        session()->flash('success', 'Klasifikasi berhasil disimpan.');
        $this->resetForm();
    }

    public function hapus(int $id): void
    {
        $isUsed = HasilPenilaian::query()->where('klasifikasi_penilaian_id', $id)->exists();
        if ($isUsed) {
            session()->flash('error', 'Klasifikasi sudah digunakan, nonaktifkan jika tidak ingin dipakai lagi.');
            return;
        }

        KlasifikasiPenilaian::query()->where('id', $id)->delete();
        session()->flash('success', 'Klasifikasi berhasil dihapus.');
    }

    public function toggleActive(int $id): void
    {
        $item = KlasifikasiPenilaian::findOrFail($id);
        $newStatus = !$item->is_active;

        if ($newStatus) {
            $overlap = KlasifikasiPenilaian::query()
                ->where('id', '!=', $item->id)
                ->where('is_active', true)
                ->where(function ($query) use ($item) {
                    $query->whereBetween('min_nilai', [$item->min_nilai, $item->max_nilai])
                        ->orWhereBetween('max_nilai', [$item->min_nilai, $item->max_nilai])
                        ->orWhere(function ($q) use ($item) {
                            $q->where('min_nilai', '<=', $item->min_nilai)
                              ->where('max_nilai', '>=', $item->max_nilai);
                        });
                })
                ->exists();

            if ($overlap) {
                session()->flash('error', 'Tidak bisa mengaktifkan klasifikasi karena rentang nilai overlap.');
                return;
            }
        }

        $item->update(['is_active' => $newStatus]);
        session()->flash('success', 'Status klasifikasi diperbarui.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">Klasifikasi Penilaian</h1>
    </x-slot>

    <main class="p-8 space-y-6">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editingId ? 'Edit Klasifikasi' : 'Tambah Klasifikasi' }}</h2>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-700 mb-1">Nama Klasifikasi</label>
                    <input wire:model="nama" type="text" class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nilai Min</label>
                    <input wire:model="minNilai" type="number" step="0.01" min="0" max="100" class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('minNilai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nilai Max</label>
                    <input wire:model="maxNilai" type="number" step="0.01" min="0" max="100" class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('maxNilai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Urutan</label>
                    <input wire:model="urutan" type="number" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('urutan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input wire:model="isActive" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="md:col-span-6 flex justify-end gap-2">
                    @if($editingId)
                        <button type="button" wire:click="resetForm" class="px-4 py-2 border border-gray-300 rounded-md text-sm">Batal</button>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Klasifikasi</h2>
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rentang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urutan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $item->nama }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($item->min_nilai, 2) }} - {{ number_format($item->max_nilai, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->urutan }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <button type="button" wire:click="toggleActive({{ $item->id }})" class="px-2 py-1 rounded text-xs {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-sm space-x-2">
                                    <button type="button" wire:click="edit({{ $item->id }})" class="px-3 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                                    <button type="button" wire:click="hapus({{ $item->id }})" class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">Belum ada data klasifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
