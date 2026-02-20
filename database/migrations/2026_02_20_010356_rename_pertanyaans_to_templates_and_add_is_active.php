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
        // Rename table pertanyaans to pertanyaan_templates
        Schema::rename('pertanyaans', 'pertanyaan_templates');

        // Add is_active column to pertanyaan_templates
        Schema::table('pertanyaan_templates', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('butuh_upload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove is_active column first
        Schema::table('pertanyaan_templates', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        // Rename table back
        Schema::rename('pertanyaan_templates', 'pertanyaans');
    }
};
