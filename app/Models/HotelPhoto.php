<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['hotel_id','path','position'];

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
        // If the path is already a full URL, return it as is
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }
        
        // Otherwise, assume it's stored in the storage and generate the URL
        return asset('storage/' . ltrim($this->path, '/'));
    }
}
