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
        // Check if columns already exist to prevent errors
        if (!Schema::hasColumn('Indirimbo', 'PlayCount')) {
            Schema::table('Indirimbo', function (Blueprint $table) {
                $table->unsignedInteger('PlayCount')->default(0)->after('Lyrics');
            });
        }
        
        if (!Schema::hasColumn('Indirimbo', 'DownloadCount')) {
            Schema::table('Indirimbo', function (Blueprint $table) {
                $table->unsignedInteger('DownloadCount')->default(0)->after('PlayCount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('Indirimbo', 'DownloadCount')) {
            Schema::table('Indirimbo', function (Blueprint $table) {
                $table->dropColumn('DownloadCount');
            });
        }
        
        if (Schema::hasColumn('Indirimbo', 'PlayCount')) {
            Schema::table('Indirimbo', function (Blueprint $table) {
                $table->dropColumn('PlayCount');
            });
        }
    }
};
