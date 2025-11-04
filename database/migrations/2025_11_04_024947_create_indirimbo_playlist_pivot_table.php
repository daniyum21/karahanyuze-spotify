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
        Schema::create('IndirimboPlaylist', function (Blueprint $table) {
            $table->unsignedInteger('IndirimboID');
            $table->unsignedInteger('PlaylistID');
            $table->unsignedInteger('StatusID')->nullable();
            $table->text('ProfilePicture')->nullable();
            $table->primary(['IndirimboID', 'PlaylistID']);
            $table->foreign('IndirimboID')->references('IndirimboID')->on('Indirimbo')->onDelete('cascade');
            $table->foreign('PlaylistID')->references('PlaylistID')->on('Playlist')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IndirimboPlaylist');
    }
};
