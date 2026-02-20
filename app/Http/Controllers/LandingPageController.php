<?php

namespace App\Http\Controllers;

use App\Models\HasilPenilaian;
use App\Models\Jadwal;
use App\Models\KlasifikasiPenilaian;
use App\Models\User;

class LandingPageController extends Controller
{
    public function index()
    {
        $jadwalAcuan = Jadwal::active()->first() ?? Jadwal::latest('tanggal_mulai')->first();
        $klasifikasiAktif = KlasifikasiPenilaian::query()->active()->orderBy('urutan')->get();

        $statistikKlasifikasi = $klasifikasiAktif->map(function ($item) use ($jadwalAcuan) {
            $query = HasilPenilaian::query()
                ->where('klasifikasi_penilaian_id', $item->id)
                ->where('status_verifikasi', 'Terverifikasi');

            if ($jadwalAcuan) {
                $query->where('jadwal_id', $jadwalAcuan->id);
            }

            return [
                'nama' => $item->nama,
                'jumlah' => $query->count(),
            ];
        });

        $statistik = [
            'total_terdaftar' => User::where('role', 'dinas')->count(),
            'klasifikasi' => $statistikKlasifikasi,
        ];

        return view('welcome', compact('statistik'));
    }
}
