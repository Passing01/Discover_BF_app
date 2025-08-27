<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistration extends Model
{
    use SoftDeletes;
    
    protected $table = 'site_event_registrations';
    
    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'email',
        'phone',
        'company',
        'position',
        'status',
        'attended_at',
        'metadata',
    ];
    
    protected $casts = [
        'attended_at' => 'datetime',
        'metadata' => 'array',
    ];
    
    protected $appends = [
        'formatted_created_at',
        'formatted_status',
    ];
    
    // Status constants
    const STATUS_REGISTERED = 'registered';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_WAITING = 'waiting';
    const STATUS_ATTENDED = 'attended';
    const STATUS_NO_SHOW = 'no_show';
    
    // Relations
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }
    
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
    
    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }
    
    public function scopeAttended($query)
    {
        return $query->where('status', self::STATUS_ATTENDED);
    }
    
    public function scopeNoShow($query)
    {
        return $query->where('status', self::STATUS_NO_SHOW);
    }
    
    // Accessors & Mutators
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->locale('fr_FR')->isoFormat('LLL');
    }
    
    public function getFormattedStatusAttribute()
    {
        $statuses = [
            self::STATUS_REGISTERED => 'Inscrit',
            self::STATUS_CONFIRMED => 'Confirmé',
            self::STATUS_CANCELLED => 'Annulé',
            self::STATUS_WAITING => 'Liste d\'attente',
            self::STATUS_ATTENDED => 'A participé',
            self::STATUS_NO_SHOW => 'Ne s\'est pas présenté',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
    
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            self::STATUS_REGISTERED => 'bg-secondary',
            self::STATUS_CONFIRMED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            self::STATUS_WAITING => 'bg-warning',
            self::STATUS_ATTENDED => 'bg-info',
            self::STATUS_NO_SHOW => 'bg-dark',
        ];
        
        return $classes[$this->status] ?? 'bg-secondary';
    }
    
    // Methods
    public function confirm()
    {
        $this->update(['status' => self::STATUS_CONFIRMED]);
        // TODO: Envoyer une notification de confirmation
    }
    
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'metadata' => array_merge($this->metadata ?? [], [
                'cancellation_reason' => $reason,
                'cancelled_at' => now()->toDateTimeString(),
            ]),
        ]);
    }
    
    public function markAsAttended()
    {
        $this->update([
            'status' => self::STATUS_ATTENDED,
            'attended_at' => now(),
        ]);
    }
    
    public function markAsNoShow()
    {
        $this->update(['status' => self::STATUS_NO_SHOW]);
    }
    
    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
    
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }
    
    public function isOnWaitingList()
    {
        return $this->status === self::STATUS_WAITING;
    }
    
    public function getCustomAnswers()
    {
        return $this->metadata['custom_answers'] ?? [];
    }
}
