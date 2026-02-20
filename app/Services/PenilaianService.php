<?php

namespace App\Services;

use App\Models\HasilPenilaian;
use App\Models\JadwalPertanyaan;
use App\Models\KlasifikasiPenilaian;
use App\Models\Penilaian;
use App\Models\Submission;
use Illuminate\Support\Collection;

class PenilaianService
{
    public function getKategoriAktifByJadwal(int $jadwalId): Collection
    {
        return JadwalPertanyaan::query()
            ->where('jadwal_pertanyaans.jadwal_id', $jadwalId)
            ->join('pertanyaan_templates', 'jadwal_pertanyaans.pertanyaan_template_id', '=', 'pertanyaan_templates.id')
            ->join('kategoris', 'pertanyaan_templates.kategori_id', '=', 'kategoris.id')
            ->select('kategoris.id', 'kategoris.nama')
            ->distinct()
            ->orderBy('kategoris.id')
            ->get();
    }

    public function getLatestSubmissionForCategory(int $userId, int $jadwalId, int $kategoriId): ?Submission
    {
        return Submission::query()
            ->where('user_id', $userId)
            ->where('jadwal_id', $jadwalId)
            ->where('kategori_id', $kategoriId)
            ->latest('tanggal_submit')
            ->latest('id')
            ->first();
    }

    public function getNilaiKategoriMap(int $userId, int $jadwalId): Collection
    {
        $kategoris = $this->getKategoriAktifByJadwal($jadwalId);

        return $kategoris->map(function ($kategori) use ($userId, $jadwalId) {
            $submission = $this->getLatestSubmissionForCategory($userId, $jadwalId, (int) $kategori->id);
            $nilai = $submission?->penilaian?->nilai;

            return [
                'kategori_id' => (int) $kategori->id,
                'kategori_nama' => $kategori->nama,
                'submission_id' => $submission?->id,
                'nilai' => $nilai !== null ? (float) $nilai : null,
            ];
        });
    }

    public function hitungNilaiAkhir(int $userId, int $jadwalId): float
    {
        $nilaiMap = $this->getNilaiKategoriMap($userId, $jadwalId);

        if ($nilaiMap->isEmpty()) {
            return 0.0;
        }

        $total = $nilaiMap->sum(fn ($item) => $item['nilai'] ?? 0.0);

        return round($total / $nilaiMap->count(), 2);
    }

    public function semuaKategoriSudahDinilai(int $userId, int $jadwalId): bool
    {
        $nilaiMap = $this->getNilaiKategoriMap($userId, $jadwalId);

        if ($nilaiMap->isEmpty()) {
            return false;
        }

        return $nilaiMap->every(fn ($item) => $item['nilai'] !== null);
    }

    public function resolveKlasifikasi(float $nilaiAkhir): ?KlasifikasiPenilaian
    {
        return KlasifikasiPenilaian::query()
            ->active()
            ->where('min_nilai', '<=', $nilaiAkhir)
            ->where('max_nilai', '>=', $nilaiAkhir)
            ->orderBy('urutan')
            ->first();
    }

    public function syncHasilPenilaian(int $userId, int $jadwalId): HasilPenilaian
    {
        $nilaiAkhir = $this->hitungNilaiAkhir($userId, $jadwalId);
        $klasifikasi = $this->resolveKlasifikasi($nilaiAkhir);

        return HasilPenilaian::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'jadwal_id' => $jadwalId,
            ],
            [
                'nilai_akhir' => $nilaiAkhir,
                'klasifikasi_penilaian_id' => $klasifikasi?->id,
            ]
        );
    }

    public function simpanNilaiKategori(int $submissionId, float $nilai): Penilaian
    {
        return Penilaian::query()->updateOrCreate(
            ['submission_id' => $submissionId],
            [
                'nilai' => $nilai,
                'status_informatif' => null,
            ]
        );
    }
}
