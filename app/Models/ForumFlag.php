<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumFlag extends Model
{
    protected $table = 'forum_flags';
    protected $primaryKey = 'FlagID';
    public $incrementing = true;

    protected $fillable = [
        'UserID',
        'flaggable_type',
        'flaggable_id',
        'reason',
        'notes',
        'is_resolved',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user who flagged
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the admin who resolved the flag
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by', 'UserID');
    }

    /**
     * Get the flaggable model (thread or comment)
     * Note: Cannot use standard morphTo because we store simple strings ('thread', 'comment')
     * instead of full class names. This accessor method handles the mapping manually.
     */
    public function getFlaggableAttribute()
    {
        $type = $this->attributes['flaggable_type'] ?? null;
        $id = $this->attributes['flaggable_id'] ?? null;
        
        if (!$type || !$id) {
            return null;
        }
        
        // Map simple types to model classes
        $map = [
            'thread' => ForumThread::class,
            'comment' => ForumComment::class,
        ];
        
        $class = $map[$type] ?? null;
        
        if (!$class) {
            return null;
        }
        
        return $class::find($id);
    }

    /**
     * Mark flag as resolved
     */
    public function resolve($adminUserId)
    {
        $this->is_resolved = true;
        $this->resolved_by = $adminUserId;
        $this->resolved_at = now();
        $this->save();
    }
}
