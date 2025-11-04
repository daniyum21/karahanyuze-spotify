<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orchestra extends Model
{
    use SoftDeletes;

    protected $table = 'Orchestres';
    protected $primaryKey = 'OrchestreID';
    public $incrementing = true;

    protected $fillable = [
        'OrchestreName',
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
        return $this->hasMany(Song::class, 'OrchestreID', 'OrchestreID');
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->OrchestreName);
    }
}
