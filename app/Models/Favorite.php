<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favorite extends Model
{
    protected $table = 'Favorites';
    protected $primaryKey = 'FavoriteID';
    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'IndirimboID', // Keep for backward compatibility
        'FavoriteType',
        'FavoriteID',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the favoritable model (polymorphic relationship)
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo('favoritable', 'FavoriteType', 'FavoriteID');
    }

    /**
     * Legacy relationship for songs (backward compatibility)
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'IndirimboID', 'IndirimboID');
    }
}
