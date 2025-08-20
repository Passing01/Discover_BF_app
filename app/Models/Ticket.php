<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ticket_type_id',
        'booking_id',
        'uuid',
        'status',
        'issued_at',
        'validated_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function type()
    {
        return $this->belongsTo(EventTicketType::class, 'ticket_type_id');
    }

    public function booking()
    {
        return $this->belongsTo(EventBooking::class, 'booking_id');
    }
}
