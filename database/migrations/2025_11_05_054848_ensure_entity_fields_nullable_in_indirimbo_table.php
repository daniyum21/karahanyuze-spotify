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
        // Ensure UmuhanziID, OrchestreID, and ItoreroID are nullable in Indirimbo table
        // Use raw SQL to modify columns to nullable since the table already exists
        if (Schema::hasTable('Indirimbo')) {
            if (Schema::hasColumn('Indirimbo', 'UmuhanziID')) {
                DB::statement('ALTER TABLE `Indirimbo` MODIFY `UmuhanziID` INT UNSIGNED NULL');
            }
            if (Schema::hasColumn('Indirimbo', 'OrchestreID')) {
                DB::statement('ALTER TABLE `Indirimbo` MODIFY `OrchestreID` INT UNSIGNED NULL');
            }
            if (Schema::hasColumn('Indirimbo', 'ItoreroID')) {
                DB::statement('ALTER TABLE `Indirimbo` MODIFY `ItoreroID` INT UNSIGNED NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a one-way migration - we can't safely reverse it without potentially breaking data
        // If needed, manually set nulls to 0 before making them NOT NULL
    }
};
