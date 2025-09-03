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
            // Tambahkan baris ini
            $table->string('status_verifikasi')->default('Menunggu')->after('total_skor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            // Tambahkan baris ini agar bisa di-rollback
            $table->dropColumn('status_verifikasi');
        });
    }
};