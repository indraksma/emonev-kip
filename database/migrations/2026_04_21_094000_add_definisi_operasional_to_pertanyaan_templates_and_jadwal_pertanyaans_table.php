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
        Schema::table('pertanyaan_templates', function (Blueprint $table) {
            $table->text('definisi_operasional')->nullable()->after('teks_pertanyaan');
        });

        Schema::table('jadwal_pertanyaans', function (Blueprint $table) {
            $table->text('definisi_operasional')->nullable()->after('teks_pertanyaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pertanyaans', function (Blueprint $table) {
            $table->dropColumn('definisi_operasional');
        });

        Schema::table('pertanyaan_templates', function (Blueprint $table) {
            $table->dropColumn('definisi_operasional');
        });
    }
};