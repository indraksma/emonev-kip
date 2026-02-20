<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klasifikasi_penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->decimal('min_nilai', 5, 2);
            $table->decimal('max_nilai', 5, 2);
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('klasifikasi_penilaians')->insert([
            [
                'nama' => 'Kurang Informatif',
                'min_nilai' => 0,
                'max_nilai' => 59.99,
                'urutan' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Cukup Informatif',
                'min_nilai' => 60,
                'max_nilai' => 79.99,
                'urutan' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Sangat Informatif',
                'min_nilai' => 80,
                'max_nilai' => 100,
                'urutan' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('klasifikasi_penilaians');
    }
};
