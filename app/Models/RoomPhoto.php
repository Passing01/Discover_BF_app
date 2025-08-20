<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['room_id','path','position'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
