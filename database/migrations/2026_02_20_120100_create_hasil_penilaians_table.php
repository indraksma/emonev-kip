<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->foreignId('klasifikasi_penilaian_id')->nullable()->constrained('klasifikasi_penilaians')->nullOnDelete();
            $table->enum('status_verifikasi', ['Menunggu', 'Terverifikasi'])->default('Menunggu');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'jadwal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_penilaians');
    }
};
