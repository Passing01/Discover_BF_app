<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Retourne l'URL complète de l'image de couverture ou null si aucune.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (empty($this->cover_image)) {
            return null;
        }

        // Si c'est déjà une URL absolue ou commence par /, la retourner telle quelle
        if (Str::startsWith($this->cover_image, ['http://', 'https://', '/'])) {
            return $this->cover_image;
        }

        // Sinon, présumer que l'image est stockée dans storage/app/public
        return asset('storage/'.$this->cover_image);
    }
}