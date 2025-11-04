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
        // Only create table if it doesn't exist (we already have existing data)
        if (!Schema::hasTable('IndirimboPlaylist')) {
            Schema::create('IndirimboPlaylist', function (Blueprint $table) {
                $table->unsignedInteger('IndirimboID');
                $table->unsignedInteger('PlaylistID');
                $table->unsignedInteger('StatusID')->nullable();
                $table->text('ProfilePicture')->nullable();
                $table->primary(['IndirimboID', 'PlaylistID']);
                
                // Add foreign keys only if they don't exist
                try {
                    $table->foreign('IndirimboID')->references('IndirimboID')->on('Indirimbo')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
                try {
                    $table->foreign('PlaylistID')->references('PlaylistID')->on('Playlist')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IndirimboPlaylist');
    }
};
