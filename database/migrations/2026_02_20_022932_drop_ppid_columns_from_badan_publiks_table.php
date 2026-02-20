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
            $table->dropColumn(['nama_ppid', 'telepon_ppid', 'email_ppid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badan_publiks', function (Blueprint $table) {
            $table->string('nama_ppid')->nullable()->after('jabatan');
            $table->string('telepon_ppid')->nullable()->after('nama_ppid');
            $table->string('email_ppid')->nullable()->after('telepon_ppid');
        });
    }
};
