<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Song extends Model
{
    use SoftDeletes;

    protected $table = 'Indirimbo';
    protected $primaryKey = 'IndirimboID';
    public $incrementing = true;

    protected $fillable = [
        'IndirimboName',
        'IndirimboUrl',
        'Description',
        'ProfilePicture',
        'IsPrivate',
        'IsFeatured',
        'StatusID',
        'UmuhanziID',
        'OrchestreID',
        'ItoreroID',
        'Lyrics',
        'UserID',
        'approved_at',
        'UUID',
        'PlayCount',
        'DownloadCount',
    ];

    protected $casts = [
        'IsPrivate' => 'boolean',
        'IsFeatured' => 'boolean',
        'deleted' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(SongStatus::class, 'StatusID', 'StatusID');
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'UmuhanziID', 'UmuhanziID');
    }

    public function orchestra(): BelongsTo
    {
        return $this->belongsTo(Orchestra::class, 'OrchestreID', 'OrchestreID');
    }

    public function itorero(): BelongsTo
    {
        return $this->belongsTo(Itorero::class, 'ItoreroID', 'ItoreroID');
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'IndirimboPlaylist', 'IndirimboID', 'PlaylistID');
    }

    public function favoritedBy()
    {
        // Support both legacy (IndirimboID) and polymorphic (FavoriteType/FavoriteID) favorites
        return $this->morphToMany(User::class, 'favoritable', 'Favorites', 'FavoriteID', 'UserID')
            ->wherePivot('FavoriteType', 'Song')
            ->withPivot('FavoriteType', 'FavoriteID')
            ->withTimestamps();
    }

    /**
     * Legacy favoritedBy relationship (backward compatibility)
     */
    public function favoritedByLegacy()
    {
        return $this->belongsToMany(User::class, 'Favorites', 'IndirimboID', 'UserID')
            ->withTimestamps();
    }

    public function owner()
    {
        if ($this->OrchestreID) {
            return $this->orchestra;
        } elseif ($this->UmuhanziID) {
            return $this->artist;
        } elseif ($this->ItoreroID) {
            return $this->itorero;
        }
        return null;
    }

    public function getSlugAttribute(): string
    {
        // Generate slug dynamically from song name
        return \Illuminate\Support\Str::slug($this->IndirimboName);
    }
}
