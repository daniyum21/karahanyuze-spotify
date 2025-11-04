<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    use SoftDeletes;

    protected $table = 'Playlist';
    protected $primaryKey = 'PlaylistID';
    public $incrementing = true;

    protected $fillable = [
        'PlaylistName',
        'Description',
        'ProfilePicture',
        'IsFeatured',
        'UUID',
    ];

    protected $casts = [
        'IsFeatured' => 'boolean',
    ];

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'IndirimboPlaylist', 'PlaylistID', 'IndirimboID');
    }

    public function publicSongs(): BelongsToMany
    {
        return $this->songs()->where('Indirimbo.StatusID', '=', 2);
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->PlaylistName);
    }

    /**
     * Get users who favorited this playlist
     */
    public function favoritedBy()
    {
        return $this->morphToMany(User::class, 'favoritable', 'Favorites', 'FavoriteID', 'UserID')
            ->wherePivot('FavoriteType', 'Playlist')
            ->withTimestamps();
    }
}
