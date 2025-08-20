<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guide_id',
        'name',
        'description',
        'duration',
        'price',
        'points_of_interest',
    ];

    protected $casts = [
        'points_of_interest' => 'array',
    ];

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class);
    }
}
