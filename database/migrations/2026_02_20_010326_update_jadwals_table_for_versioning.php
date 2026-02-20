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
        Schema::table('jadwals', function (Blueprint $table) {
            $table->string('nama')->nullable()->after('id');
            $table->year('tahun')->nullable()->after('nama');
            $table->boolean('is_active')->default(true)->after('tanggal_selesai');
            $table->text('deskripsi')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropColumn(['nama', 'tahun', 'is_active', 'deskripsi']);
        });
    }
};
