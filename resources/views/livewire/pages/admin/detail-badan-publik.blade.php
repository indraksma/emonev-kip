<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        // Eager load relasi untuk menghindari query N+1
        $this->user = $user->load(['badanPublik', 'hasilPenilaians.klasifikasiPenilaian']);
    }

    // DIHAPUS: Fungsi resetPassword() tidak lagi diperlukan
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.badan-publik') }}" wire:navigate class="p-2 rounded-full hover:bg-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Badan Publik</h1>
        </div>
    </x-slot>

    <main class="p-8">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    Detail Badan Publik: {{ $user->badanPublik->nama_badan_publik ?? 'N/A' }}
                </h2>
                <a href="{{ route('admin.badan-publik.edit', ['user' => $user->id]) }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Edit Informasi
                </a>
            </div>

            <div class="mt-6 space-y-8">
                {{-- Helper function untuk menampilkan item data --}}
                @php
                    function displayInfo($label, $value) {
                        echo '<div class="flex flex-col">';
                        echo '<label class="text-sm font-medium text-gray-500">' . htmlspecialchars($label) . '</label>';
                        echo '<p class="mt-1 text-gray-900 p-3 bg-gray-50 rounded-md border border-gray-200">' . htmlspecialchars($value ?? '-') . '</p>';
                        echo '</div>';
                    }
                @endphp

                {{-- Informasi Umum & Kontak --}}
                <fieldset>
                    <legend class="text-base font-semibold text-gray-900 mb-2">Informasi Umum & Kontak</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{ displayInfo('Nama Badan Publik', $user->badanPublik->nama_badan_publik) }}
                        {{ displayInfo('Website', $user->badanPublik->website) }}
                        {{ displayInfo('No. Telepon', $user->badanPublik->telepon_badan_publik) }}
                        {{ displayInfo('Email', $user->badanPublik->email_badan_publik) }}
                        <div class="md:col-span-2">
                            {{ displayInfo('Alamat', $user->badanPublik->alamat) }}
                        </div>
                    </div>
                </fieldset>

                {{-- Informasi Responden Utama --}}
                <fieldset>
                    <legend class="text-base font-semibold text-gray-900 mb-2">Informasi Responden Utama</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{ displayInfo('Nama Responden', $user->name) }}
                        {{ displayInfo('No. Telepon', $user->badanPublik->telepon_responden) }}
                        {{ displayInfo('Jabatan', $user->badanPublik->jabatan) }}
                        {{ displayInfo('Email Responden (untuk login)', $user->email) }}
                    </div>
                </fieldset>

                {{-- Riwayat Hasil Kuesioner --}}
                <fieldset>
                    <legend class="text-base font-semibold text-gray-900 mb-2">Riwayat Hasil Kuesioner</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Ringkasan hasil penilaian final per jadwal --}}
                        @php
                            $hasilPenilaians = $user->hasilPenilaians;
                            $averageScore = $hasilPenilaians->avg('nilai_akhir');
                            $statusModes = $hasilPenilaians->pluck('klasifikasiPenilaian.nama')->filter()->mode();
                            $commonStatus = $statusModes[0] ?? null;
                        @endphp
                        {{ displayInfo('Nilai Rata-rata', $averageScore ? number_format($averageScore, 2) : 'Belum ada') }}
                        {{ displayInfo('Status Umum', $commonStatus ?? 'Belum ada') }}
                    </div>
                </fieldset>

                {{-- Informasi Akun E-Monev --}}
                <fieldset>
                    <legend class="text-base font-semibold text-gray-900 mb-2">Informasi Akun E-Monev KIP</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{ displayInfo('Email', $user->email) }}
                        <div>
                            <label class="text-sm font-medium text-gray-500">Password</label>
                            <div class="mt-1">
                                <p class="text-gray-900 p-3 bg-gray-50 rounded-md border border-gray-200 tracking-widest">••••••••</p>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </main>
</div>
