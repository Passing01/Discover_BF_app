<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountFeature extends Model
{
    protected $fillable = [
        'account_id',
        'feature',
        'is_active',
        'settings',
        'activated_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public static function getAvailableFeatures(): array
    {
        return [
            'hotel' => [
                'name' => 'Hôtel',
                'description' => 'Gestion des hôtels, chambres et réservations',
                'settings' => [
                    'max_rooms' => 'integer|min:1|max:1000',
                    'enable_online_checkin' => 'boolean',
                    'enable_housekeeping' => 'boolean',
                ],
            ],
            'restaurant' => [
                'name' => 'Restaurant',
                'description' => 'Gestion des restaurants, menus et réservations',
                'settings' => [
                    'max_tables' => 'integer|min:1|max:500',
                    'enable_online_ordering' => 'boolean',
                    'enable_delivery' => 'boolean',
                ],
            ],
            'transport' => [
                'name' => 'Transport',
                'description' => 'Gestion des bus, taxis et réservations',
                'settings' => [
                    'max_vehicles' => 'integer|min:1|max:500',
                    'enable_route_planning' => 'boolean',
                ],
            ],
            'event' => [
                'name' => 'Événementiel',
                'description' => 'Organisation et gestion d\'événements',
                'settings' => [
                    'max_events' => 'integer|min:1|max:100',
                    'enable_ticketing' => 'boolean',
                ],
            ],
            'flight' => [
                'name' => 'Vols',
                'description' => 'Gestion des vols et réservations',
                'settings' => [
                    'max_flights' => 'integer|min:1|max:1000',
                    'enable_online_checkin' => 'boolean',
                ],
            ],
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
