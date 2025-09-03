<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->enum('tipe_jawaban', ['Ya/Tidak'])->default('Ya/Tidak')->after('teks_pertanyaan');
            $table->boolean('butuh_link')->default(false)->after('tipe_jawaban');
            $table->boolean('butuh_upload')->default(false)->after('butuh_link');
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->dropColumn(['tipe_jawaban', 'butuh_link', 'butuh_upload']);
        });
    }
};