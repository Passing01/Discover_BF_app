<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'profile_picture',
        'is_active',
        'role_onboarded_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'role_onboarded_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function organizerProfile()
    {
        return $this->hasOne(OrganizerProfile::class, 'user_id');
    }

    public function hotelBookings()
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function tourBookings()
    {
        return $this->hasMany(TourBooking::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }

    public function arProgress()
    {
        return $this->hasMany(UserArProgress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuide()
    {
        return $this->role === 'guide';
    }

    public function isRestaurant()
    {
        return $this->role === 'restaurant';
    }

    // Breeze nav expects a name
    public function getNameAttribute()
    {
        $full = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
        return $full !== '' ? $full : ($this->attributes['email'] ?? '');
    }
}
