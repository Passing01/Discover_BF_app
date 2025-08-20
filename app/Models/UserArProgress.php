<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserArProgress extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'ar_location_id',
        'visit_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function arLocation()
    {
        return $this->belongsTo(ArLocation::class);
    }
}
