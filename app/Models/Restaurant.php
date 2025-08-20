<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'owner_id', 'name', 'slug', 'address', 'city', 'latitude', 'longitude', 'map_url', 'phone', 'email',
        'description', 'avg_price', 'rating', 'is_active', 'cover_image', 'gallery', 'video_urls',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'avg_price' => 'decimal:2',
        'rating' => 'float',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'gallery' => 'array',
        'video_urls' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function reservations()
    {
        return $this->hasMany(RestaurantReservation::class);
    }
}
