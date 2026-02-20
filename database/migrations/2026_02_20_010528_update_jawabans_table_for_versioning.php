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
        Schema::table('jawabans', function (Blueprint $table) {
            // Check if pertanyaan_id column still exists
            if (Schema::hasColumn('jawabans', 'pertanyaan_id')) {
                // Drop old foreign key constraint first
                $table->dropForeign(['pertanyaan_id']);

                // Rename column (make it nullable for now)
                $table->renameColumn('pertanyaan_id', 'jadwal_pertanyaan_id');
            }
            // NOTE: If column already renamed (from partial migration), skip rename
            // Foreign key constraint will be added in migrate_existing_data_to_versioning
            // after the jadwal_pertanyaans table is populated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawabans', function (Blueprint $table) {
            // Drop new foreign key constraint
            $table->dropForeign(['jadwal_pertanyaan_id']);

            // Rename column back
            $table->renameColumn('jadwal_pertanyaan_id', 'pertanyaan_id');

            // Add old foreign key constraint
            $table->foreign('pertanyaan_id')->references('id')->on('jadwal_pertanyaans')->onDelete('cascade');
        });
    }
};
