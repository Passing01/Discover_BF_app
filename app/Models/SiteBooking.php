<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SiteBooking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'site_id',
        'user_id',
        'visit_date',
        'visitors_count',
        'total_amount',
        'status',
        'special_requests'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'total_amount' => 'decimal:2',
        'visitors_count' => 'integer',
    ];

    /**
     * Get the site that owns the booking.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the user that made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Get the booking status with a badge.
     */
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
        ];

        $color = $statuses[$this->status] ?? 'secondary';
        
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }
    
    /**
     * Scope a query to filter bookings based on status and search term.
     */
    public function scopeFilter($query, array $filters)
    {
        // Filtre par statut
        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });
        
        // Filtre par terme de recherche
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhereHas('site', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        });
        
        return $query;
    }
}
