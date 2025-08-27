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
    
    /**
     * Obtenir les médias associés à l'événement
     */
    public function media()
    {
        return $this->hasMany(Media::class)->orderBy('order');
    }
    
    /**
     * Obtenir l'image à la une de l'événement
     */
    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }
    
    /**
     * Obtenir les inscriptions à l'événement
     */
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Obtenir la catégorie de l'événement
     */
    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
    
    /**
     * Obtenir le nombre d'inscriptions confirmées
     */
    public function getConfirmedRegistrationsCountAttribute()
    {
        return $this->registrations()->where('status', 'confirmed')->count();
    }
    
    /**
     * Vérifier si l'événement est complet
     */
    public function getIsFullAttribute()
    {
        if (is_null($this->capacity)) {
            return false;
        }
        
        return $this->confirmed_registrations_count >= $this->capacity;
    }
    
    /**
     * Obtenir la plage de dates formatée
     */
    public function getFormattedDateRangeAttribute()
    {
        $start = $this->start_date->format('d/m/Y H:i');
        $end = $this->end_date->format('d/m/Y H:i');
        
        if ($this->start_date->isSameDay($this->end_date)) {
            return $start . ' - ' . $this->end_date->format('H:i');
        }
        
        return $start . ' au ' . $end;
    }
}
