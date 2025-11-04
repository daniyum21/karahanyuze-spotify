<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'Favorites';
    protected $primaryKey = 'FavoriteID';
    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'IndirimboID',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function song()
    {
        return $this->belongsTo(Song::class, 'IndirimboID', 'IndirimboID');
    }
}
