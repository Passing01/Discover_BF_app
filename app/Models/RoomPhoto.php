<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'room_id',
        'path',
        'is_main',
        'uploaded_by',
        'position',
        'alt_text',
        'caption'
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getThumbnailUrlAttribute()
    {
        // Si une version miniature existe, on la retourne
        if (Storage::exists('thumbs/' . $this->path)) {
            return Storage::url('thumbs/' . $this->path);
        }
        
        // Sinon, on retourne l'URL de l'image originale
        return $this->url;
    }

    protected static function booted()
    {
        static::deleting(function ($photo) {
            // Supprimer le fichier physique lors de la suppression
            Storage::delete($photo->path);
            
            // Supprimer la miniature si elle existe
            if (Storage::exists('thumbs/' . $photo->path)) {
                Storage::delete('thumbs/' . $photo->path);
            }
        });
    }
}
