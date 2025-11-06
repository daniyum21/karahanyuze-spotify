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
        'declined_reason',
        'declined_at',
        'declined_by',
        'UUID',
        'PlayCount',
        'DownloadCount',
    ];

    protected $casts = [
        'IsPrivate' => 'boolean',
        'IsFeatured' => 'boolean',
        'deleted' => 'boolean',
        'approved_at' => 'datetime',
        'declined_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function declinedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declined_by', 'UserID');
    }

    public function listeningHistory()
    {
        return $this->hasMany(ListeningHistory::class, 'IndirimboID', 'IndirimboID');
    }

    public function isApproved(): bool
    {
        $approvedStatus = SongStatus::where('StatusName', 'Approved')
            ->orWhere('StatusName', 'approved')
            ->orWhere('StatusName', 'Public')
            ->orWhere('StatusName', 'public')
            ->first();
        
        return $approvedStatus && $this->StatusID === $approvedStatus->StatusID;
    }

    public function isDeclined(): bool
    {
        return !is_null($this->declined_at);
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
