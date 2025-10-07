<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HotelPhoto extends Model
{
    use HasFactory, HasUuids;

    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'hotel_id',
        'path',
        'is_main',
        'original_name',
        'mime_type',
        'size',
        'position'
    ];

    protected $appends = ['url', 'thumbnail_url'];
    
    protected $casts = [
        'is_main' => 'boolean',
        'size' => 'integer',
        'position' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the URL for the hotel photo
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        if (empty($this->path)) {
            return null;
        }

        // If the path is already a full URL, return it as is
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }
        
        // Otherwise, assume it's stored in the storage and generate the URL
        if (Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->url($this->path);
        }
        
        return null;
    }

    /**
     * Get the thumbnail URL for the photo
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if (empty($this->path)) {
            return null;
        }
        
        // Si c'est déjà une URL complète, on essaie de générer une miniature
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            // Ici, vous pourriez utiliser un service de CDN pour les miniatures
            // Par exemple, avec Cloudinary, Imgix, etc.
            return $this->url . '?w=300&h=200&fit=crop';
        }
        
        // Pour le stockage local, on pourrait utiliser Intervention Image
        // mais pour l'instant on retourne simplement l'URL normale
        return $this->url;
    }
    
    /**
     * Get the file size in a human readable format
     *
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->size) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->size;
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }
}
