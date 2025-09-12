<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Site extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'city',
        'category',
        'description',
        'price_min',
        'price_max',
        'latitude',
        'longitude',
        'photo_url',
        'manager_id',
        'address',
        'phone',
        'email',
        'website',
        'opening_hours',
        'is_active'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'price_min' => 'float',
        'price_max' => 'float',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    /**
     * Get the manager that owns the site.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the bookings for the site.
     */
    public function bookings()
    {
        return $this->hasMany(SiteBooking::class);
    }
}
