<?php

namespace App\Models\Site;

use App\Models\Account\Account;
use App\Models\Site\TouristSite;
use App\Traits\HasMediaTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Tags\HasTags;

class Event extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasMediaTrait, HasTags;
    
    protected $table = 'site_events';
    
    protected $fillable = [
        'site_id',
        'category_id',
        'title',
        'slug',
        'description',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'timezone',
        'location_name',
        'address',
        'city',
        'region',
        'postal_code',
        'country',
        'meeting_url',
        'meeting_instructions',
        'max_participants',
        'registration_deadline',
        'is_featured',
        'is_free',
        'status',
        'postponed_from',
        'postponed_reason',
        'cancelled_at',
        'cancellation_reason',
        'completed_at',
        'metadata',
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'registration_deadline' => 'datetime',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'max_participants' => 'integer',
        'postponed_from' => 'date',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];
    
    protected $appends = [
        'featured_image_url',
        'formatted_date',
        'formatted_time',
        'registration_count',
        'is_registration_open',
        'has_available_spots',
    ];
    
    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
            ->where('status', 'published')
            ->orderBy('start_date');
    }
    
    public function scopePast($query)
    {
        return $query->where('end_date', '<', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('end_date', 'desc');
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    // Relations
    public function site()
    {
        return $this->belongsTo(TouristSite::class, 'site_id');
    }
    
    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
    
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }
    
    // Accessors & Mutators
    public function getFeaturedImageUrlAttribute()
    {
        return $this->hasMedia('featured') 
            ? $this->getFirstMediaUrl('featured')
            : asset('assets_admin/img/placeholder-event.jpg');
    }
    
    public function getFormattedDateAttribute()
    {
        $start = $this->start_date->locale('fr_FR')->isoFormat('D MMMM YYYY');
        
        if ($this->end_date && !$this->start_date->isSameDay($this->end_date)) {
            $end = $this->end_date->locale('fr_FR')->isoFormat('D MMMM YYYY');
            return "Du $start au $end";
        }
        
        return $start;
    }
    
    public function getFormattedTimeAttribute()
    {
        if (!$this->start_time) {
            return null;
        }
        
        $startTime = Carbon::parse($this->start_time)->format('H:i');
        
        if ($this->end_time) {
            $endTime = Carbon::parse($this->end_time)->format('H:i');
            return "De $startTime à $endTime";
        }
        
        return "À partir de $startTime";
    }
    
    public function getRegistrationCountAttribute()
    {
        return $this->registrations()->count();
    }
    
    public function getIsRegistrationOpenAttribute()
    {
        if ($this->status !== 'published') {
            return false;
        }
        
        if ($this->registration_deadline) {
            return now()->lte($this->registration_deadline);
        }
        
        return now()->lt($this->start_date);
    }
    
    public function getHasAvailableSpotsAttribute()
    {
        if (is_null($this->max_participants)) {
            return true;
        }
        
        return $this->registrations()->count() < $this->max_participants;
    }
    
    public function getLocationAttribute()
    {
        if ($this->meeting_url) {
            return 'En ligne';
        }
        
        $location = [];
        if ($this->location_name) {
            $location[] = $this->location_name;
        }
        if ($this->city) {
            $location[] = $this->city;
        }
        if ($this->country) {
            $location[] = $this->country;
        }
        
        return implode(', ', $location);
    }
    
    // Methods
    public function registerParticipant(array $data)
    {
        if (!$this->is_registration_open) {
            throw new \Exception('Les inscriptions pour cet événement sont fermées.');
        }
        
        if (!$this->has_available_spots) {
            throw new \Exception('Désolé, il n\'y a plus de places disponibles pour cet événement.');
        }
        
        return $this->registrations()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'position' => $data['position'] ?? null,
            'status' => 'registered',
            'metadata' => [
                'custom_answers' => $data['custom_answers'] ?? [],
            ],
        ]);
    }
    
    public function updateStatus($status, $reason = null)
    {
        $updates = ['status' => $status];
        
        if ($status === 'cancelled') {
            $updates['cancelled_at'] = now();
            $updates['cancellation_reason'] = $reason;
        } elseif ($status === 'completed') {
            $updates['completed_at'] = now();
        } elseif ($status === 'postponed' && $reason) {
            $updates['postponed_reason'] = $reason;
        }
        
        return $this->update($updates);
    }
    
    public function isUpcoming()
    {
        return $this->start_date->isFuture() && $this->status === 'published';
    }
    
    public function isOngoing()
    {
        $now = now();
        return $this->status === 'published' && 
               $this->start_date->lte($now) && 
               (!$this->end_date || $this->end_date->gte($now));
    }
    
    public function isPast()
    {
        return $this->end_date ? 
            $this->end_date->isPast() : 
            $this->start_date->isPast();
    }
    
    public function isCancelled()
    {
        return $this->status === 'cancelled' || $this->cancelled_at !== null;
    }
    
    public function isPostponed()
    {
        return $this->status === 'postponed' || $this->postponed_from !== null;
    }
    
    // Media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
            
        $this->addMediaCollection('gallery')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
