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
        if (!Schema::hasTable('Indirimbo')) {
            Schema::create('Indirimbo', function (Blueprint $table) {
                $table->increments('IndirimboID');
                $table->string('IndirimboName');
                $table->string('IndirimboUrl');
                $table->text('Description')->nullable();
                $table->text('ProfilePicture')->nullable();
                $table->boolean('IsPrivate')->default(0);
                $table->boolean('IsFeatured')->default(0);
                $table->unsignedInteger('StatusID')->default(1);
                $table->unsignedInteger('UmuhanziID')->nullable();
                $table->unsignedInteger('OrchestreID')->nullable();
                $table->unsignedInteger('ItoreroID')->nullable();
                $table->text('Lyrics')->nullable();
                $table->unsignedInteger('UserID');
                $table->boolean('deleted')->default(0);
                $table->timestamp('approved_at')->nullable();
                $table->string('UUID');
                $table->timestamps();
                $table->softDeletes();

                // Add foreign keys only if they don't exist
                try {
                    $table->foreign('StatusID')->references('StatusID')->on('IndirimboStatus')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
                try {
                    $table->foreign('UmuhanziID')->references('UmuhanziID')->on('Abahanzi')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
                try {
                    $table->foreign('OrchestreID')->references('OrchestreID')->on('Orchestres')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
                try {
                    $table->foreign('ItoreroID')->references('ItoreroID')->on('Amatorero')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
                try {
                    $table->foreign('UserID')->references('UserID')->on('Users')->onDelete('cascade');
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
        Schema::dropIfExists('Indirimbo');
    }
};
