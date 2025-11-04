<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongStatus extends Model
{
    use SoftDeletes;

    protected $table = 'IndirimboStatus';
    protected $primaryKey = 'StatusID';
    public $incrementing = true;

    protected $fillable = [
        'StatusName',
        'UUID',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'StatusID', 'StatusID');
    }
}
