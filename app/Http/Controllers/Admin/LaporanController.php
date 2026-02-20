<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penilaian;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function unduhPdf(Request $request)
    {
        $kategoriId = $request->query('kategoriId');

        $query = Penilaian::with(['submission.user.badanPublik', 'submission.kategori']);

        if ($kategoriId && $kategoriId !== 'semua') {
            $query->whereHas('submission', function ($q) use ($kategoriId) {
                $q->where('kategori_id', $kategoriId);
            });
        }

        $laporans = $query->get();
        $namaKategori = $kategoriId && $kategoriId !== 'semua' ? \App\Models\Kategori::find($kategoriId)->nama : 'Semua Kategori';

        $pdf = Pdf::loadView('admin.laporan-pdf', [
            'laporans' => $laporans,
            'namaKategori' => $namaKategori,
            'tanggal' => now()->isoFormat('D MMMM YYYY')
        ]);

        return $pdf->download('laporan-e-monev-kip-' . now()->format('Y-m-d') . '.pdf');
    }
}
