<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Itorero extends Model
{
    use SoftDeletes;

    protected $table = 'Amatorero';
    protected $primaryKey = 'ItoreroID';
    public $incrementing = true;

    protected $fillable = [
        'ItoreroName',
        'Description',
        'ProfilePicture',
        'IsFeatured',
        'declined_reason',
        'declined_at',
        'declined_by',
        'UUID',
    ];

    protected $casts = [
        'IsFeatured' => 'boolean',
        'declined_at' => 'datetime',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'ItoreroID', 'ItoreroID');
    }

    public function declinedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declined_by', 'UserID');
    }

    public function isDeclined(): bool
    {
        return !is_null($this->declined_at);
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->ItoreroName);
    }

    /**
     * Get users who favorited this itorero
     */
    public function favoritedBy()
    {
        return $this->morphToMany(User::class, 'favoritable', 'Favorites', 'FavoriteID', 'UserID')
            ->wherePivot('FavoriteType', 'Itorero')
            ->withTimestamps();
    }
}
