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
        Schema::create('jadwal_pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->foreignId('pertanyaan_template_id')->nullable()->constrained('pertanyaan_templates')->onDelete('set null');
            $table->text('teks_pertanyaan');
            $table->integer('urutan')->default(0);

            // Frozen fields from template
            $table->enum('tipe_jawaban', ['Ya/Tidak'])->default('Ya/Tidak');
            $table->boolean('butuh_link')->default(false);
            $table->boolean('butuh_upload')->default(false);

            $table->timestamps();

            $table->index(['jadwal_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pertanyaans');
    }
};
