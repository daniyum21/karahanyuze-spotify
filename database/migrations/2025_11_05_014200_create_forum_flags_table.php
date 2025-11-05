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
        Schema::create('forum_flags', function (Blueprint $table) {
            $table->id('FlagID');
            $table->unsignedInteger('UserID'); // User who flagged
            $table->string('flaggable_type'); // 'thread' or 'comment'
            $table->unsignedBigInteger('flaggable_id'); // ID of thread or comment
            $table->string('reason')->nullable(); // Optional reason for flagging
            $table->text('notes')->nullable(); // Additional notes
            $table->boolean('is_resolved')->default(false);
            $table->unsignedInteger('resolved_by')->nullable(); // Admin who resolved it
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('UserID');
            $table->index(['flaggable_type', 'flaggable_id']);
            $table->index('is_resolved');
            
            // Ensure a user can only flag the same item once
            $table->unique(['UserID', 'flaggable_type', 'flaggable_id']);
        });
        
        // Add foreign keys using raw SQL
        try {
            DB::statement('ALTER TABLE `forum_flags` ADD CONSTRAINT `forum_flags_userid_foreign` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE');
            DB::statement('ALTER TABLE `forum_flags` ADD CONSTRAINT `forum_flags_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `Users` (`UserID`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Foreign keys might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_flags');
    }
};
