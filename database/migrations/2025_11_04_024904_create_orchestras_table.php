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
        if (!Schema::hasTable('Orchestres')) {
            Schema::create('Orchestres', function (Blueprint $table) {
                $table->increments('OrchestreID');
                $table->string('OrchestreName');
                $table->text('Description')->nullable();
                $table->text('ProfilePicture')->nullable();
                $table->boolean('IsFeatured')->default(0);
                $table->string('UUID');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Orchestres');
    }
};
