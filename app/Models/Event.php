<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'organizer_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'ticket_price',
        'category',
        'latitude',
        'longitude',
        'image_path',
        'ticket_template_id',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function bookings()
    {
        return $this->hasMany(EventBooking::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(EventTicketType::class);
    }

    public function ticketTemplate()
    {
        return $this->belongsTo(TicketTemplate::class);
    }
}
