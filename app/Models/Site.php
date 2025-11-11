<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Accessor: Unified public URL for the site's main image.
     */
    public function getImageUrlAttribute(): ?string
    {
        $path = $this->photo_url ?? null;
        if (!$path) return null;
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) return $path;
        return Storage::url($path);
    }
}
