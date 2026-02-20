<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HasilPenilaian;
use App\Models\KlasifikasiPenilaian;
use Barryvdh\DomPDF\Facade\Pdf;

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
}
