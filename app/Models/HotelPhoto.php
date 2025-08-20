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
}
