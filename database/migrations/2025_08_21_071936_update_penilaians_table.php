<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('penilaians', function (Blueprint $table) {
            // Hapus kolom-kolom lama
            $table->dropForeign(['user_id']); // Hapus relasi dulu
            $table->dropColumn('user_id');
            $table->dropColumn('status');
            $table->dropColumn('total_skor');

            // Tambah kolom-kolom baru
            $table->foreignId('submission_id')->after('id')->constrained('submissions')->onDelete('cascade');
            $table->integer('nilai')->nullable()->after('submission_id');
            $table->enum('status_informatif', [
                'Sangat Informatif',
                'Cukup Informatif',
                'Kurang Informatif'
            ])->nullable()->after('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            //
        });
    }
};
