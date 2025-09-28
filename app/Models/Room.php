<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Room extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'type',
        'description',
        'price_per_night',
        'capacity',
        'available',
        'hotel_id'
    ];
    
    // These are virtual attributes that will be stored in the description JSON
    protected $appends = [
        'size',
        'view',
        'bed_type',
        'bed_count',
        'max_occupancy',
        'is_smoking_allowed',
        'has_balcony',
        'has_terrace',
        'has_sea_view',
        'has_lake_view',
        'has_mountain_view',
        'has_bathtub',
        'has_shower',
        'has_air_conditioning',
        'has_heating',
        'has_tv',
        'has_phone',
        'has_safe',
        'has_mini_bar',
        'has_electric_kettle',
        'has_wifi',
        'is_accessible',
        'min_stay',
        'max_adults',
        'max_children',
        'room_number',
        'floor'
    ];
    
    protected $casts = [
        'available' => 'boolean',
        'price_per_night' => 'float',
        'capacity' => 'integer',
        'description' => 'array'
    ];
    
    // Accessors and mutators for the virtual attributes
    protected function getDescriptionAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }
    
    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = is_string($value) ? $value : json_encode($value);
    }

    // Room type is now stored as a string in the 'type' column
    public function getTypeNameAttribute()
    {
        return $this->type; // This will return the room type as a string
    }
    
    /**
     * Get all photos for the room.
     */
    public function photos()
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(HotelBooking::class);
    }
    
    /**
     * Vérifie si la chambre est disponible pour les dates données
     *
     * @param string $checkIn Date d'arrivée (format Y-m-d)
     * @param string $checkOut Date de départ (format Y-m-d)
     * @return bool
     */
    public function isAvailable($checkIn, $checkOut)
    {
        // Vérifier d'abord si la chambre est marquée comme disponible
        if (!$this->available) {
            return false;
        }
        
        // Vérifier les réservations existantes qui se chevauchent avec les dates demandées
        $hasConflictingBooking = $this->bookings()
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->where(function($q) use ($checkIn, $checkOut) {
                    // La réservation commence pendant le séjour demandé
                    $q->where('start_date', '>=', $checkIn)
                      ->where('start_date', '<', $checkOut);
                })->orWhere(function($q) use ($checkIn, $checkOut) {
                    // La réservation se termine pendant le séjour demandé
                    $q->where('end_date', '>', $checkIn)
                      ->where('end_date', '<=', $checkOut);
                })->orWhere(function($q) use ($checkIn, $checkOut) {
                    // La réservation englobe complètement le séjour demandé
                    $q->where('start_date', '<=', $checkIn)
                      ->where('end_date', '>=', $checkOut);
                });
            })
            ->whereIn('status', ['confirmed', 'pending', 'checked_in'])
            ->exists();
            
        return !$hasConflictingBooking;
    }

    /**
     * Get all amenities for the room through the hotel.
     */
    public function amenities()
    {
        return $this->hotel ? $this->hotel->amenities() : collect();
    }

    /**
     * Get all of the reviews for the room.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
    
    /**
     * Get the average rating for the room.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }
    
    // Accessors for virtual attributes stored in the description JSON
    public function getSizeAttribute()
    {
        return $this->description['size'] ?? null;
    }
    
    public function getViewAttribute()
    {
        return $this->description['view'] ?? null;
    }
    
    public function getBedTypeAttribute()
    {
        return $this->description['bed_type'] ?? 'double';
    }
    
    public function getBedCountAttribute()
    {
        return $this->description['bed_count'] ?? 1;
    }
    
    public function getMaxOccupancyAttribute()
    {
        return $this->description['max_occupancy'] ?? $this->capacity;
    }
    
    public function getIsSmokingAllowedAttribute()
    {
        return $this->description['is_smoking_allowed'] ?? false;
    }
    
    public function getHasBalconyAttribute()
    {
        return $this->description['has_balcony'] ?? false;
    }
    
    public function getHasTerraceAttribute()
    {
        return $this->description['has_terrace'] ?? false;
    }
    
    public function getHasSeaViewAttribute()
    {
        return $this->description['has_sea_view'] ?? false;
    }
    
    public function getHasLakeViewAttribute()
    {
        return $this->description['has_lake_view'] ?? false;
    }
    
    public function getHasMountainViewAttribute()
    {
        return $this->description['has_mountain_view'] ?? false;
    }
    
    public function getHasBathtubAttribute()
    {
        return $this->description['has_bathtub'] ?? false;
    }
    
    public function getHasShowerAttribute()
    {
        return $this->description['has_shower'] ?? true;
    }
    
    public function getHasAirConditioningAttribute()
    {
        return $this->description['has_air_conditioning'] ?? false;
    }
    
    public function getHasHeatingAttribute()
    {
        return $this->description['has_heating'] ?? false;
    }
    
    public function getHasTvAttribute()
    {
        return $this->description['has_tv'] ?? false;
    }
    
    public function getHasPhoneAttribute()
    {
        return $this->description['has_phone'] ?? false;
    }
    
    public function getHasSafeAttribute()
    {
        return $this->description['has_safe'] ?? false;
    }
    
    public function getHasMiniBarAttribute()
    {
        return $this->description['has_mini_bar'] ?? false;
    }
    
    public function getHasElectricKettleAttribute()
    {
        return $this->description['has_electric_kettle'] ?? false;
    }
    
    public function getHasWifiAttribute()
    {
        return $this->description['has_wifi'] ?? false;
    }
    
    public function getIsAccessibleAttribute()
    {
        return $this->description['is_accessible'] ?? false;
    }
    
    public function getMinStayAttribute()
    {
        return $this->description['min_stay'] ?? 1;
    }
    
    public function getMaxAdultsAttribute()
    {
        return $this->description['max_adults'] ?? $this->capacity;
    }
    
    public function getMaxChildrenAttribute()
    {
        return $this->description['max_children'] ?? 0;
    }
    
    public function getRoomNumberAttribute()
    {
        return $this->description['room_number'] ?? null;
    }
    
    public function getFloorAttribute()
    {
        return $this->description['floor'] ?? 0;
    }
}
