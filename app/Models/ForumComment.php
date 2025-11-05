<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumComment extends Model
{
    use SoftDeletes;

    protected $table = 'forum_comments';
    protected $primaryKey = 'CommentID';
    public $incrementing = true;

    protected $fillable = [
        'ThreadID',
        'UserID',
        'parent_id',
        'body',
        'is_edited',
        'edited_at',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'is_approved' => 'boolean',
        'edited_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the thread this comment belongs to
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'ThreadID', 'ThreadID');
    }

    /**
     * Get the user who created the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the parent comment (if this is a reply)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumComment::class, 'parent_id', 'CommentID');
    }

    /**
     * Get replies to this comment (only approved)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'parent_id', 'CommentID')
            ->where('is_approved', true)
            ->with('thread')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get the admin who approved the comment
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'UserID');
    }

    /**
     * Get flags for this comment
     */
    public function flags(): HasMany
    {
        return $this->hasMany(ForumFlag::class, 'flaggable_id', 'CommentID')
            ->where('flaggable_type', 'comment');
    }

    /**
     * Mark comment as edited
     */
    public function markAsEdited()
    {
        $this->is_edited = true;
        $this->edited_at = now();
        $this->save();
    }
}
