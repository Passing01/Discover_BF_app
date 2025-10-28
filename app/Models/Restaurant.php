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

    /**
     * Resolve a media path or URL to a displayable URL.
     */
    protected function resolveMediaUrl(null|string|array $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        // If it's an array, try common keys or first element
        if (is_array($value)) {
            $value = $value['path'] ?? $value['url'] ?? ($value[0] ?? null);
            if (empty($value)) {
                return null;
            }
        }
        // If already absolute URL
        if (is_string($value) && preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }
        // If path is in public assets (with or without leading slash)
        if (is_string($value)) {
            $trimmed = ltrim($value, '/');
            // Common public asset prefixes used in this app
            $isPublicAsset = str_starts_with($trimmed, 'assets/')
                || str_starts_with($trimmed, 'assets_restaurant/')
                || str_starts_with($trimmed, 'assets_admin/')
                || file_exists(public_path($trimmed));
            if ($isPublicAsset) {
                return asset($trimmed);
            }
        }
        // Default to storage public disk
        return is_string($value) ? \Storage::url($value) : null;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->cover_image);
    }

    /**
     * @return array<int, string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        $images = is_array($this->gallery) ? $this->gallery : [];
        $urls = [];
        foreach ($images as $image) {
            $url = $this->resolveMediaUrl($image);
            if (is_string($url) && $url !== '') {
                $urls[] = $url;
            }
        }
        return $urls;
    }
}
