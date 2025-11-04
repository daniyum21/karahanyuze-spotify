<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'Users';
    protected $primaryKey = 'UserID';
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'PublicName',
        'FirstName',
        'LastName',
        'Email',
        'UserName',
        'password',
        'StatusID',
        'RoleID',
        'UUID',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Don't cast password to 'hashed' - passwords from old DB are already hashed
            'deleted_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'UserID';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Check if user is admin (RoleID == 1)
     */
    public function isAdmin()
    {
        return $this->RoleID == 1;
    }

    /**
     * Get the user's favorite songs
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'UserID', 'UserID');
    }

    /**
     * Get the user's favorite songs (many-to-many relationship)
     * Legacy method for backward compatibility
     */
    public function favoriteSongs()
    {
        return $this->belongsToMany(Song::class, 'Favorites', 'UserID', 'IndirimboID')
            ->wherePivot('FavoriteType', 'Song')
            ->orWherePivotNull('FavoriteType') // Support legacy favorites without FavoriteType
            ->withTimestamps();
    }

    /**
     * Get all favorites (polymorphic)
     */
    public function allFavorites()
    {
        return $this->hasMany(Favorite::class, 'UserID', 'UserID');
    }

    /**
     * Get favorite artists
     */
    public function favoriteArtists()
    {
        return $this->morphedByMany(Artist::class, 'favoritable', 'Favorites', 'UserID', 'FavoriteID')
            ->wherePivot('FavoriteType', 'Artist')
            ->withTimestamps();
    }

    /**
     * Get favorite orchestras
     */
    public function favoriteOrchestras()
    {
        return $this->morphedByMany(Orchestra::class, 'favoritable', 'Favorites', 'UserID', 'FavoriteID')
            ->wherePivot('FavoriteType', 'Orchestra')
            ->withTimestamps();
    }

    /**
     * Get favorite itoreros
     */
    public function favoriteItoreros()
    {
        return $this->morphedByMany(Itorero::class, 'favoritable', 'Favorites', 'UserID', 'FavoriteID')
            ->wherePivot('FavoriteType', 'Itorero')
            ->withTimestamps();
    }

    /**
     * Get favorite playlists
     */
    public function favoritePlaylists()
    {
        return $this->morphedByMany(Playlist::class, 'favoritable', 'Favorites', 'UserID', 'FavoriteID')
            ->wherePivot('FavoriteType', 'Playlist')
            ->withTimestamps();
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification()
    {
        return $this->Email;
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Determine if the user has verified their email address.
     * Admins don't need email verification.
     */
    public function hasVerifiedEmail()
    {
        // Admins don't need email verification
        if ($this->isAdmin()) {
            return true;
        }
        
        // For regular users, check if email_verified_at is not null
        return !is_null($this->email_verified_at);
    }

    /**
     * Send the email verification notification.
     * Override to use our custom notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }
}
