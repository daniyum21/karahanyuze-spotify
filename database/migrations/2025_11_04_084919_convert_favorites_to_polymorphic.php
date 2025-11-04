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
        // Add polymorphic columns to Favorites table
        if (Schema::hasTable('Favorites')) {
            // Add FavoriteType column if it doesn't exist
            if (!Schema::hasColumn('Favorites', 'FavoriteType')) {
                Schema::table('Favorites', function (Blueprint $table) {
                    $table->string('FavoriteType', 50)->nullable()->after('UserID');
                    $table->unsignedInteger('FavoriteID')->nullable()->after('FavoriteType');
                });
            }
            
            // Migrate existing IndirimboID data to polymorphic format
            DB::table('Favorites')
                ->whereNotNull('IndirimboID')
                ->whereNull('FavoriteType')
                ->update([
                    'FavoriteType' => 'Song',
                    'FavoriteID' => DB::raw('IndirimboID')
                ]);
            
            // Add unique constraint for polymorphic favorites
            try {
                DB::statement('ALTER TABLE `Favorites` ADD UNIQUE INDEX `favorites_user_favoritable_unique` (`UserID`, `FavoriteType`, `FavoriteID`)');
            } catch (\Exception $e) {
                // Index might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('Favorites')) {
            // Remove polymorphic columns
            if (Schema::hasColumn('Favorites', 'FavoriteID')) {
                Schema::table('Favorites', function (Blueprint $table) {
                    $table->dropColumn('FavoriteID');
                });
            }
            
            if (Schema::hasColumn('Favorites', 'FavoriteType')) {
                Schema::table('Favorites', function (Blueprint $table) {
                    $table->dropColumn('FavoriteType');
                });
            }
            
            // Drop unique index
            try {
                DB::statement('ALTER TABLE `Favorites` DROP INDEX `favorites_user_favoritable_unique`');
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
        }
    }
};
