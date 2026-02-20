<?php

use App\Services\PenilaianService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $service = app(PenilaianService::class);

        $pairs = DB::table('submissions')
            ->select('user_id', 'jadwal_id')
            ->distinct()
            ->get();

        foreach ($pairs as $pair) {
            if (!$pair->jadwal_id) {
                continue;
            }

            $hasil = $service->syncHasilPenilaian((int) $pair->user_id, (int) $pair->jadwal_id);

            if ($service->semuaKategoriSudahDinilai((int) $pair->user_id, (int) $pair->jadwal_id)) {
                $hasil->update([
                    'status_verifikasi' => 'Terverifikasi',
                    'verified_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('hasil_penilaians')->truncate();
    }
};
