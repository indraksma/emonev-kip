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
        Schema::table('badan_publiks', function (Blueprint $table) {
            // Data Badan Publik
            $table->string('nama_badan_publik')->after('user_id');
            $table->string('website')->after('nama_badan_publik');
            $table->string('telepon_badan_publik')->after('website');
            $table->string('email_badan_publik')->after('telepon_badan_publik');
            $table->text('alamat')->after('email_badan_publik');

            // Data Responden
            $table->string('telepon_responden')->after('alamat');
            $table->string('jabatan')->after('telepon_responden');

            // Data PPID
            $table->string('nama_ppid')->after('jabatan');
            $table->string('telepon_ppid')->after('nama_ppid');
            $table->string('email_ppid')->after('telepon_ppid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badan_publiks', function (Blueprint $table) {
            $table->dropColumn([
                'nama_badan_publik',
                'website',
                'telepon_badan_publik',
                'email_badan_publik',
                'alamat',
                'telepon_responden',
                'jabatan',
                'nama_ppid',
                'telepon_ppid',
                'email_ppid',
            ]);
        });
    }
};
