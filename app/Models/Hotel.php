<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Hotel extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'description',
        'stars',
        'photo',
        'latitude',
        'longitude',
        'manager_id',
    ];

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function rules()
    {
        return $this->belongsToMany(StayRule::class, 'hotel_rule', 'hotel_id', 'rule_id');
    }

    public function photos()
    {
        return $this->hasMany(HotelPhoto::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(HotelBooking::class, Room::class);
    }

    public function activeBookings()
    {
        return $this->hasManyThrough(
            HotelBooking::class, 
            Room::class
        )->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
