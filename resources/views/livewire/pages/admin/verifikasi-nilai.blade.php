<?php

use App\Models\HasilPenilaian;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\PenilaianService;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component
{
    public User $user;
    public Jadwal $jadwal;
    public array $kategoriSections = [];
    public array $nilaiKategori = [];

    public function mount(User $user, Jadwal $jadwal): void
    {
        $this->user = $user->load('badanPublik');
        $this->jadwal = $jadwal;
        $this->loadKategoriSections();
    }

    public function loadKategoriSections(): void
    {
        $service = app(PenilaianService::class);
        $kategoris = $service->getKategoriAktifByJadwal($this->jadwal->id);

        $sections = [];
        foreach ($kategoris as $kategori) {
            $submission = $service->getLatestSubmissionForCategory($this->user->id, $this->jadwal->id, (int) $kategori->id);
            $answers = $submission
                ? $submission->jawaban()->with('jadwalPertanyaan')->orderBy('jadwal_pertanyaan_id')->get()
                : collect();

            $sections[] = [
                'kategori_id' => (int) $kategori->id,
                'kategori_nama' => $kategori->nama,
                'submission_id' => $submission?->id,
                'tanggal_submit' => $submission?->tanggal_submit,
                'jawabans' => $answers,
            ];

            $this->nilaiKategori[(int) $kategori->id] = $submission?->penilaian?->nilai;
        }

        $this->kategoriSections = $sections;
        app(PenilaianService::class)->syncHasilPenilaian($this->user->id, $this->jadwal->id);
    }

    public function simpanNilaiKategori(int $kategoriId): void
    {
        $value = $this->nilaiKategori[$kategoriId] ?? null;
        $validator = Validator::make(
            ['nilai' => $value],
            ['nilai' => 'required|numeric|min:0|max:100']
        );

        if ($validator->fails()) {
            $this->addError('nilaiKategori.' . $kategoriId, $validator->errors()->first('nilai'));
            return;
        }

        $section = collect($this->kategoriSections)->firstWhere('kategori_id', $kategoriId);
        if (!$section || !$section['submission_id']) {
            session()->flash('error', 'Kategori belum memiliki submission, sehingga tidak bisa dinilai.');
            return;
        }

        app(PenilaianService::class)->simpanNilaiKategori((int) $section['submission_id'], (float) $value);
        app(PenilaianService::class)->syncHasilPenilaian($this->user->id, $this->jadwal->id);

        session()->flash('success', 'Nilai kategori berhasil disimpan.');
        $this->loadKategoriSections();
    }

    public function selesaiVerifikasi(): void
    {
        $service = app(PenilaianService::class);

        if (!$service->semuaKategoriSudahDinilai($this->user->id, $this->jadwal->id)) {
            session()->flash('error', 'Semua kategori aktif harus dinilai terlebih dahulu sebelum verifikasi final.');
            return;
        }

        $hasil = $service->syncHasilPenilaian($this->user->id, $this->jadwal->id);
        $hasil->update([
            'status_verifikasi' => 'Terverifikasi',
            'verified_at' => now(),
        ]);

        session()->flash('success', 'Verifikasi nilai dinas berhasil diselesaikan.');
        $this->redirectRoute('admin.penilaian', navigate: true);
    }

    public function with(): array
    {
        $hasil = HasilPenilaian::query()
            ->with('klasifikasiPenilaian')
            ->where('user_id', $this->user->id)
            ->where('jadwal_id', $this->jadwal->id)
            ->first();

        return ['hasil' => $hasil];
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

    <main class="p-8 space-y-6">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nama Dinas / Badan Publik</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->badanPublik->nama_badan_publik ?? $user->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Jadwal Penilaian</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $jadwal->nama }} ({{ $jadwal->tahun }})</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nilai Akhir</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $hasil ? number_format($hasil->nilai_akhir, 2) : '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Klasifikasi</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $hasil?->klasifikasiPenilaian?->nama ?? 'Belum terklasifikasi' }}</p>
                </div>
            </div>
        </div>

        @forelse ($kategoriSections as $section)
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $section['kategori_nama'] }}</h2>
                            <p class="text-sm text-gray-500">
                                Tanggal submit: {{ $section['tanggal_submit'] ? \Carbon\Carbon::parse($section['tanggal_submit'])->isoFormat('D MMMM YYYY') : 'Belum ada submission' }}
                            </p>
                        </div>
                        <div class="w-full md:w-auto md:ml-6 md:flex-shrink-0">
                            <label class="block text-sm font-medium text-gray-700 mb-1 md:text-right">Nilai Kategori (0-100)</label>
                            <div class="flex items-center gap-2 md:justify-end">
                                <input
                                    wire:model="nilaiKategori.{{ $section['kategori_id'] }}"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="w-32 border-gray-300 rounded-md shadow-sm text-sm"
                                >
                                <button
                                    wire:click="simpanNilaiKategori({{ $section['kategori_id'] }})"
                                    type="button"
                                    class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 whitespace-nowrap"
                                >
                                    Simpan
                                </button>
                            </div>
                            @error('nilaiKategori.' . $section['kategori_id'])
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pertanyaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jawaban</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link Dokumen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upload Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($section['jawabans'] as $answer)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $answer->jadwalPertanyaan->teks_pertanyaan ?? 'Pertanyaan tidak ditemukan' }}</td>
                                    <td class="px-6 py-4 text-sm font-bold {{ $answer->jawaban === 'Ya' ? 'text-green-600' : 'text-red-600' }}">{{ $answer->jawaban ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($answer->link_dokumen)
                                            <a href="{{ $answer->link_dokumen }}" target="_blank" class="text-blue-600 hover:underline">Lihat Link</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($answer->upload_dokumen)
                                            <a href="{{ asset('storage/' . $answer->upload_dokumen) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Dokumen</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada jawaban pada kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-lg shadow-md text-sm text-gray-500">
                Tidak ada kategori aktif pada jadwal ini.
            </div>
        @endforelse

        <div class="bg-white p-6 rounded-lg shadow-md flex justify-end space-x-3">
            <a href="{{ route('admin.penilaian') }}" wire:navigate class="px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Kembali
            </a>
            <button wire:click="selesaiVerifikasi" wire:loading.attr="disabled" class="px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400">
                Selesai Verifikasi
            </button>
        </div>
    </main>
</div>
