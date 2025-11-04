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
        // Check if email_verified_at column already exists
        if (!Schema::hasColumn('Users', 'email_verified_at')) {
            Schema::table('Users', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable()->after('Email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('Users', 'email_verified_at')) {
            Schema::table('Users', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }
    }
};
