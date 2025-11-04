<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use SoftDeletes;

    protected $table = 'Abahanzi';
    protected $primaryKey = 'UmuhanziID';
    public $incrementing = true;

    protected $fillable = [
        'FirstName',
        'LastName',
        'Email',
        'Twitter',
        'StageName',
        'ProfilePicture',
        'Description',
        'IsFeatured',
        'UUID',
    ];

    protected $casts = [
        'IsFeatured' => 'boolean',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'UmuhanziID', 'UmuhanziID');
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->StageName);
    }
}
