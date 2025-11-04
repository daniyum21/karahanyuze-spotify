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
        // Check if table exists (case-insensitive)
        if (!Schema::hasTable('Favorites') && !Schema::hasTable('favorites')) {
            Schema::create('Favorites', function (Blueprint $table) {
                $table->id('FavoriteID');
                $table->unsignedInteger('UserID');
                $table->unsignedInteger('IndirimboID');
                $table->timestamps();
                
                // Unique constraint: a user can only favorite a song once
                $table->unique(['UserID', 'IndirimboID']);
            });
            
            // Add foreign keys using raw SQL to match old database casing
            try {
                DB::statement('ALTER TABLE `Favorites` ADD CONSTRAINT `favorites_userid_foreign` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE');
                DB::statement('ALTER TABLE `Favorites` ADD CONSTRAINT `favorites_indirimboid_foreign` FOREIGN KEY (`IndirimboID`) REFERENCES `Indirimbo` (`IndirimboID`) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Foreign keys might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Favorites');
    }
};
