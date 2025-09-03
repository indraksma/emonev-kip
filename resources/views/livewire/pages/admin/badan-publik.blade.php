<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public function with(): array
    {
        $query = User::query()
            ->where('role', 'dinas')
            ->with('badanPublik'); // Eager load the badanPublik relationship

        if (!empty($this->search)) {
            $query->whereHas('badanPublik', function ($q) {
                $q->where('nama_badan_publik', 'like', '%' . $this->search . '%');
            })->orWhere('name', 'like', '%' . $this->search . '%'); // Also search by respondent name
        }

        return [
            'users' => $query->latest()->paginate(10),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-8">
            <h1 class="text-3xl font-bold text-gray-900">Badan Publik</h1>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari sesuatu..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </x-slot>

    <main class="p-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Badan Publik Dinas Banjarnegara</h2>
                {{-- Add a search input specific to the table if needed --}}
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badan Publik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responden Utama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Website</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->badanPublik->nama_badan_publik ?? 'Belum diisi' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->badanPublik->website ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {{-- DIUBAH: Mengarahkan ke route detail --}}
                                    <a href="{{ route('admin.badan-publik.detail', ['user' => $user->id]) }}" wire:navigate class="px-3 py-1 text-xs font-medium text-white bg-gray-800 rounded-md hover:bg-gray-900">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data badan publik yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </main>
</div>
