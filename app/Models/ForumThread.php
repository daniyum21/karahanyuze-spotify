<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ForumThread extends Model
{
    use SoftDeletes;

    protected $table = 'forum_threads';
    protected $primaryKey = 'ThreadID';
    public $incrementing = true;

    protected $fillable = [
        'title',
        'body',
        'slug',
        'UserID',
        'is_locked',
        'is_pinned',
        'is_approved',
        'approved_by',
        'approved_at',
        'view_count',
        'comment_count',
        'last_comment_at',
        'last_comment_user_id',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_pinned' => 'boolean',
        'is_approved' => 'boolean',
        'view_count' => 'integer',
        'comment_count' => 'integer',
        'last_comment_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            if (empty($thread->slug)) {
                $thread->slug = Str::slug($thread->title);
                
                // Ensure uniqueness
                $originalSlug = $thread->slug;
                $count = 1;
                while (static::where('slug', $thread->slug)->exists()) {
                    $thread->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    /**
     * Get the user who created the thread
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the user who made the last comment
     */
    public function lastCommentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_comment_user_id', 'UserID');
    }

    /**
     * Get the admin who approved the thread
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'UserID');
    }

    /**
     * Get all comments for this thread
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'ThreadID', 'ThreadID')
            ->whereNull('parent_id') // Only top-level comments
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get all comments including nested ones
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'ThreadID', 'ThreadID')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get flags for this thread
     */
    public function flags(): HasMany
    {
        return $this->hasMany(ForumFlag::class, 'flaggable_id', 'ThreadID')
            ->where('flaggable_type', 'thread');
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    /**
     * Update comment count
     */
    public function updateCommentCount()
    {
        $this->comment_count = $this->allComments()->count();
        $lastComment = $this->allComments()->latest()->first();
        if ($lastComment) {
            $this->last_comment_at = $lastComment->created_at;
            $this->last_comment_user_id = $lastComment->UserID;
        }
        $this->save();
    }
}
