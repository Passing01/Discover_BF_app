<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountUser extends Model
{
    protected $table = 'account_users';
    
    protected $fillable = [
        'account_id',
        'user_id',
        'role',
        'permissions',
        'is_primary_contact',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_primary_contact' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    public static function getAvailableRoles(): array
    {
        return [
            'owner' => 'Propriétaire',
            'admin' => 'Administrateur',
            'manager' => 'Gestionnaire',
            'staff' => 'Personnel',
        ];
    }

    public static function getDefaultPermissions(string $role): array
    {
        return match($role) {
            'owner' => [
                'account:manage',
                'users:manage',
                'billing:manage',
                'settings:manage',
                'reports:view',
            ],
            'admin' => [
                'users:manage',
                'reports:view',
            ],
            'manager' => [
                'bookings:manage',
                'inventory:manage',
            ],
            'staff' => [
                'bookings:view',
                'checkin:process',
            ],
            default => [],
        };
    }
}
