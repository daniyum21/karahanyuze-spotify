<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure UmuhanziID, OrchestreID, and ItoreroID are nullable in Indirimbo table
        // Use raw SQL to modify columns to nullable since the table already exists
        // Try-catch each statement to handle cases where columns might already be nullable
        try {
            DB::statement('ALTER TABLE `Indirimbo` MODIFY `UmuhanziID` INT UNSIGNED NULL');
        } catch (\Exception $e) {
            // Column might already be nullable or doesn't exist - that's okay
            Log::info('Migration: UmuhanziID already nullable or error: ' . $e->getMessage());
        }
        
        try {
            DB::statement('ALTER TABLE `Indirimbo` MODIFY `OrchestreID` INT UNSIGNED NULL');
        } catch (\Exception $e) {
            // Column might already be nullable or doesn't exist - that's okay
            Log::info('Migration: OrchestreID already nullable or error: ' . $e->getMessage());
        }
        
        try {
            DB::statement('ALTER TABLE `Indirimbo` MODIFY `ItoreroID` INT UNSIGNED NULL');
        } catch (\Exception $e) {
            // Column might already be nullable or doesn't exist - that's okay
            Log::info('Migration: ItoreroID already nullable or error: ' . $e->getMessage());
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
