<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListeningHistory extends Model
{
    protected $table = 'listening_history';
    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'IndirimboID',
        'played_at',
        'play_duration',
    ];

    protected $casts = [
        'played_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, 'IndirimboID', 'IndirimboID');
    }
}

