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
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id('CommentID');
            $table->unsignedBigInteger('ThreadID'); // Which thread this comment belongs to
            $table->unsignedInteger('UserID'); // Author of the comment
            $table->unsignedBigInteger('parent_id')->nullable(); // For nested replies (null = top-level comment)
            $table->text('body');
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('ThreadID');
            $table->index('UserID');
            $table->index('parent_id');
            $table->index('created_at');
        });
        
        // Add foreign keys using raw SQL
        try {
            DB::statement('ALTER TABLE `forum_comments` ADD CONSTRAINT `forum_comments_threadid_foreign` FOREIGN KEY (`ThreadID`) REFERENCES `forum_threads` (`ThreadID`) ON DELETE CASCADE');
            DB::statement('ALTER TABLE `forum_comments` ADD CONSTRAINT `forum_comments_userid_foreign` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE');
            DB::statement('ALTER TABLE `forum_comments` ADD CONSTRAINT `forum_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forum_comments` (`CommentID`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Foreign keys might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};
