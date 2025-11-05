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
        // Make ProfilePicture nullable in IndirimboPlaylist table
        if (Schema::hasTable('IndirimboPlaylist')) {
            if (Schema::hasColumn('IndirimboPlaylist', 'ProfilePicture')) {
                // Use raw SQL to modify the column to nullable
                DB::statement('ALTER TABLE `IndirimboPlaylist` MODIFY `ProfilePicture` TEXT NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make ProfilePicture non-nullable again (this might fail if there are null values)
        if (Schema::hasTable('IndirimboPlaylist')) {
            if (Schema::hasColumn('IndirimboPlaylist', 'ProfilePicture')) {
                // First, set all null ProfilePicture to empty string
                DB::table('IndirimboPlaylist')
                    ->whereNull('ProfilePicture')
                    ->update(['ProfilePicture' => '']);
                
                // Then make it non-nullable
                DB::statement('ALTER TABLE `IndirimboPlaylist` MODIFY `ProfilePicture` TEXT NOT NULL');
            }
        }
    }
};
