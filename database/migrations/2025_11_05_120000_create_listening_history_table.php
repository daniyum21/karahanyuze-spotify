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
        if (!Schema::hasTable('listening_history')) {
            Schema::create('listening_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('UserID');
                $table->unsignedInteger('IndirimboID');
                $table->timestamp('played_at');
                $table->integer('play_duration')->nullable(); // Duration in seconds
                $table->timestamps();
                
                $table->index(['UserID', 'played_at']);
                $table->index(['IndirimboID', 'played_at']);
                
                // Note: Foreign keys are commented out due to case sensitivity issues
                // The application will work fine without them for now
                // They can be added manually later if needed for data integrity
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_history');
    }
};

