<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RestaurantReservation extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'restaurant_id', 'reservation_at', 'party_size', 'status', 'special_requests', 'order_items',
        'payment_status', 'payment_intent_id', 'amount_paid'
    ];

    protected $casts = [
        'reservation_at' => 'datetime',
        'party_size' => 'integer',
        'order_items' => 'array',
        'amount_paid' => 'integer',
    ];

    // Statuts de paiement
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
