<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $fillable = [
        'event_id',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'type',
        'is_featured',
        'order',
        'alt_text',
        'description'
    ];
    
    protected $casts = [
        'is_featured' => 'boolean',
        'size' => 'integer',
        'order' => 'integer'
    ];
    
    /**
     * Relation avec l'événement
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Obtenir l'URL publique du média
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
    
    /**
     * Vérifier si le média est une image
     */
    public function isImage(): bool
    {
        return strpos($this->mime_type, 'image/') === 0;
    }
    
    /**
     * Obtenir la taille formatée du fichier
     */
    public function getFormattedSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->size;
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
