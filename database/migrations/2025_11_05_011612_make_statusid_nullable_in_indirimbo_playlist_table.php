<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make StatusID nullable in IndirimboPlaylist table
        if (Schema::hasTable('IndirimboPlaylist')) {
            if (Schema::hasColumn('IndirimboPlaylist', 'StatusID')) {
                // Use raw SQL to modify the column to nullable
                DB::statement('ALTER TABLE `IndirimboPlaylist` MODIFY `StatusID` INT UNSIGNED NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make StatusID non-nullable again (this might fail if there are null values)
        if (Schema::hasTable('IndirimboPlaylist')) {
            if (Schema::hasColumn('IndirimboPlaylist', 'StatusID')) {
                // First, set all null StatusID to 0
                DB::table('IndirimboPlaylist')
                    ->whereNull('StatusID')
                    ->update(['StatusID' => 0]);
                
                // Then make it non-nullable
                DB::statement('ALTER TABLE `IndirimboPlaylist` MODIFY `StatusID` INT UNSIGNED NOT NULL');
            }
        }
    }
};
