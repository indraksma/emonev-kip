<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadanPublik;
use App\Models\HasilPenilaian;
use App\Models\Jadwal;
use App\Models\KlasifikasiPenilaian;
use App\Services\PenilaianService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function unduhPdf(Request $request)
    {
        $klasifikasiId = $request->query('klasifikasiId');

        $query = HasilPenilaian::with(['user.badanPublik', 'jadwal', 'klasifikasiPenilaian'])
            ->where('status_verifikasi', 'Terverifikasi');

        if ($klasifikasiId && $klasifikasiId !== 'semua') {
            $query->where('klasifikasi_penilaian_id', $klasifikasiId);
        }

        $laporans = $query->get();
        $namaKlasifikasi = $klasifikasiId && $klasifikasiId !== 'semua'
            ? KlasifikasiPenilaian::find($klasifikasiId)?->nama
            : 'Semua Klasifikasi';

        $pdf = Pdf::loadView('admin.laporan-pdf', [
            'laporans' => $laporans,
            'namaKlasifikasi' => $namaKlasifikasi,
            'tanggal' => now()->isoFormat('D MMMM YYYY')
        ]);

        return $pdf->download('laporan-e-monev-kip-' . now()->format('Y-m-d') . '.pdf');
    }

    public function unduhPdfPerBadanPublik(Request $request, int $userId, int $jadwalId)
    {
        $badanPublik = BadanPublik::where('user_id', $userId)->firstOrFail();
        $jadwal = Jadwal::findOrFail($jadwalId);

        $hasilPenilaian = HasilPenilaian::query()
            ->where('user_id', $userId)
            ->where('jadwal_id', $jadwalId)
            ->where('status_verifikasi', 'Terverifikasi')
            ->firstOrFail();

        // Get scores per category using PenilaianService
        $service = app(PenilaianService::class);
        $nilaiPerKategori = $service->getNilaiKategoriMap($userId, $jadwalId);

        $pdf = Pdf::loadView('admin.laporan-per-badan-publik-pdf', [
            'badanPublik' => $badanPublik,
            'jadwal' => $jadwal,
            'hasilPenilaian' => $hasilPenilaian,
            'nilaiPerKategori' => $nilaiPerKategori,
            'tanggal' => now()->isoFormat('D MMMM YYYY')
        ]);

        $namaFile = 'laporan-' . Str::slug($badanPublik->nama_badan_publik) . '-' . $jadwal->nama . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($namaFile);
    }
}
