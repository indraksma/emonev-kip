<?php

use App\Models\BadanPublik;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public ?BadanPublik $badanPublik;

    /**
     * Mount the component and fetch the user's data.
     */
    public function mount(): void
    {
        $user = Auth::user()->load('badanPublik');
        $this->badanPublik = $user->badanPublik;
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
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Biodata Peserta</h1>
                    <a href="{{ route('biodata.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z"></path></svg>
                        Ubah Biodata Peserta
                    </a>
                </div>

                @if($badanPublik)
                <div class="bg-white p-8 rounded-lg shadow-md space-y-8">
                    <!-- Data Display Item -->
                    @php
                        function dataItem($label, $value) {
                            echo '<div class="py-3 px-4 border border-gray-200 rounded-md">';
                            echo '<p class="text-sm text-gray-500">' . htmlspecialchars($label) . '</p>';
                            echo '<p class="font-medium text-gray-800">' . htmlspecialchars($value ?? '-') . '</p>';
                            echo '</div>';
                        }
                    @endphp

                    <!-- Data Badan Publik -->
                    <fieldset>
                        <legend class="text-lg font-semibold text-gray-900 mb-4">Data Badan Publik</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{ dataItem('Nama Badan Publik', $badanPublik->nama_badan_publik) }}
                            {{ dataItem('Website', $badanPublik->website) }}
                            {{ dataItem('No. Telepon', $badanPublik->telepon_badan_publik) }}
                            {{ dataItem('Email', $badanPublik->email_badan_publik) }}
                            <div class="md:col-span-2">
                                {{ dataItem('Alamat', $badanPublik->alamat) }}
                            </div>
                        </div>
                    </fieldset>

                    <!-- Data Responden -->
                    <fieldset>
                        <legend class="text-lg font-semibold text-gray-900 mb-4">Data Responden</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{ dataItem('Nama Responden', Auth::user()->name) }}
                            {{ dataItem('No. Telepon', $badanPublik->telepon_responden) }}
                            {{ dataItem('Jabatan', $badanPublik->jabatan) }}
                            {{ dataItem('Email Responden (untuk login)', Auth::user()->email) }}
                        </div>
                    </fieldset>

                    <!-- Data PPID -->
                    <fieldset>
                        <legend class="text-lg font-semibold text-gray-900 mb-4">Data PPID</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{ dataItem('Nama PPID', $badanPublik->nama_ppid) }}
                            {{ dataItem('No. Telepon', $badanPublik->telepon_ppid) }}
                            <div class="md:col-span-2">
                                {{ dataItem('Email PPID', $badanPublik->email_ppid) }}
                            </div>
                        </div>
                    </fieldset>
                </div>
                @else
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <p>Data biodata tidak ditemukan. Silakan hubungi admin.</p>
                </div>
                @endif
            </div>
        </main>
    </div>

