<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DishOrder extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'restaurant_id', 'dish_id', 'quantity', 'delivery_address', 'delivery_lat', 'delivery_lng', 'delivery_time', 'notes', 'status', 'total_price'
    ];

    protected $casts = [
        'delivery_time' => 'datetime',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'delivery_lat' => 'decimal:7',
        'delivery_lng' => 'decimal:7',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function dish() { return $this->belongsTo(Dish::class); }
}
