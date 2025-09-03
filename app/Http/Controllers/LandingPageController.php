<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Asumsi model User untuk dinas/PPID
use App\Models\Penilaian; // Asumsi ada model Penilaian

class LandingPageController extends Controller
{
    public function index()
    {
        // 1. Menghitung PPID yang terdaftar
        // Asumsi setiap dinas yang mendaftar adalah satu user dengan role 'dinas'
        $totalTerdaftar = User::where('role', 'dinas')->count();

        // 2. Menghitung status penilaian
        // Asumsi ada tabel 'penilaians' dengan kolom 'status'
        // Status bisa berisi: 'Menuju Informatif', 'Kurang Informatif', dll.
        $menujuInformatif = Penilaian::where('status', 'Menuju Informatif')->count();
        $kurangInformatif = Penilaian::where('status', 'Kurang Informatif')->count();
        $cukupInformatif = Penilaian::where('status', 'Cukup Informatif')->count();
        $sangatInformatif = Penilaian::where('status', 'Sangat Informatif')->count();

        // Gabungkan semua data ke dalam satu array
        $statistik = [
            'total_terdaftar' => $totalTerdaftar,
            'menuju_informatif' => $menujuInformatif,
            'kurang_informatif' => $kurangInformatif,
            'cukup_informatif' => $cukupInformatif,
            'sangat_informatif' => $sangatInformatif,
        ];

        // Kirim data ke view 'welcome'
        return view('welcome', compact('statistik'));
    }
}