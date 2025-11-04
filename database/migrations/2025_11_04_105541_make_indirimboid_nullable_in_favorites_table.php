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
        // Make IndirimboID nullable to support polymorphic favorites (playlists, artists, etc.)
        if (Schema::hasTable('Favorites')) {
            if (Schema::hasColumn('Favorites', 'IndirimboID')) {
                // Use raw SQL to modify the column to nullable
                DB::statement('ALTER TABLE `Favorites` MODIFY `IndirimboID` INT UNSIGNED NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make IndirimboID non-nullable again (this might fail if there are null values)
        if (Schema::hasTable('Favorites')) {
            if (Schema::hasColumn('Favorites', 'IndirimboID')) {
                // First, set all null IndirimboID to 0 for non-song favorites
                DB::table('Favorites')
                    ->whereNull('IndirimboID')
                    ->whereNotNull('FavoriteType')
                    ->where('FavoriteType', '!=', 'Song')
                    ->update(['IndirimboID' => 0]);
                
                // Then make it non-nullable
                DB::statement('ALTER TABLE `Favorites` MODIFY `IndirimboID` INT UNSIGNED NOT NULL');
            }
        }
    }
};
