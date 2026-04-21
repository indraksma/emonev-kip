<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create default jadwal for existing data
        // First, check if there are any submissions
        $firstSubmission = DB::table('submissions')->orderBy('created_at')->first();

        if ($firstSubmission) {
            $defaultJadwalId = DB::table('jadwals')->insertGetId([
                'nama' => 'Migrasi Data Historis',
                'tahun' => date('Y', strtotime($firstSubmission->created_at)),
                'tanggal_mulai' => DB::raw('(SELECT DATE(MIN(created_at)) FROM submissions)'),
                'tanggal_selesai' => now(),
                'is_active' => false,
                'deskripsi' => 'Jadwal default untuk data yang sudah ada sebelum sistem versioning',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Create snapshot of all existing pertanyaan_templates
            DB::statement("
                INSERT INTO jadwal_pertanyaans (
                    jadwal_id,
                    pertanyaan_template_id,
                    teks_pertanyaan,
                    definisi_operasional,
                    urutan,
                    tipe_jawaban,
                    butuh_link,
                    butuh_upload,
                    created_at,
                    updated_at
                )
                SELECT
                    {$defaultJadwalId},
                    id,
                    teks_pertanyaan,
                    definisi_operasional,
                    id,
                    tipe_jawaban,
                    butuh_link,
                    butuh_upload,
                    NOW(),
                    NOW()
                FROM pertanyaan_templates
            ");

            // 3. Update all existing submissions
            DB::table('submissions')
                ->whereNull('jadwal_id')
                ->update(['jadwal_id' => $defaultJadwalId]);

            // 4. Update jawabans with new jadwal_pertanyaan_id
            DB::statement("
                UPDATE jawabans j
                INNER JOIN (
                    SELECT
                        pt.id as old_id,
                        jp.id as new_id
                    FROM pertanyaan_templates pt
                    INNER JOIN jadwal_pertanyaans jp ON jp.pertanyaan_template_id = pt.id
                    WHERE jp.jadwal_id = {$defaultJadwalId}
                ) mapping ON j.jadwal_pertanyaan_id = mapping.old_id
                SET j.jadwal_pertanyaan_id = mapping.new_id
            ");

            // 5. Add foreign key constraint to jawabans table
            Schema::table('jawabans', function (Blueprint $table) {
                $table->foreign('jadwal_pertanyaan_id')->references('id')->on('jadwal_pertanyaans')->onDelete('cascade');
            });

            // 6. Make jadwal_id NOT NULL in submissions
            Schema::table('submissions', function (Blueprint $table) {
                $table->foreignId('jadwal_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data migration, so we can't easily rollback
        // In production, you would restore from backup
        // For development, we'll leave the data as-is
    }
};
