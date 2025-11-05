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
        Schema::table('Indirimbo', function (Blueprint $table) {
            $table->text('declined_reason')->nullable()->after('approved_at');
            $table->timestamp('declined_at')->nullable()->after('declined_reason');
            $table->unsignedBigInteger('declined_by')->nullable()->after('declined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Indirimbo', function (Blueprint $table) {
            $table->dropColumn(['declined_reason', 'declined_at', 'declined_by']);
        });
    }
};
