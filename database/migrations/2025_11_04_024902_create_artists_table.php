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
        Schema::create('Abahanzi', function (Blueprint $table) {
            $table->increments('UmuhanziID');
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('Email');
            $table->string('Twitter')->nullable();
            $table->string('StageName');
            $table->string('ProfilePicture')->nullable();
            $table->text('Description')->nullable();
            $table->boolean('IsFeatured')->default(0);
            $table->string('UUID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Abahanzi');
    }
};
