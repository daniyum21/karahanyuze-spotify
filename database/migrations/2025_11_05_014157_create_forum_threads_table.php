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
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id('ThreadID');
            $table->string('title');
            $table->text('body');
            $table->string('slug')->unique();
            $table->unsignedInteger('UserID'); // Author of the thread
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->timestamp('last_comment_at')->nullable();
            $table->unsignedInteger('last_comment_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('UserID');
            $table->index('is_pinned');
            $table->index('last_comment_at');
            $table->index('slug');
        });
        
        // Add foreign key using raw SQL to match database casing
        try {
            DB::statement('ALTER TABLE `forum_threads` ADD CONSTRAINT `forum_threads_userid_foreign` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE');
            DB::statement('ALTER TABLE `forum_threads` ADD CONSTRAINT `forum_threads_last_comment_user_id_foreign` FOREIGN KEY (`last_comment_user_id`) REFERENCES `Users` (`UserID`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Foreign keys might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_threads');
    }
};
