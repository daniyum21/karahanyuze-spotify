<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'UUID',
    ];

    protected $casts = [
        'IsFeatured' => 'boolean',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'ItoreroID', 'ItoreroID');
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
