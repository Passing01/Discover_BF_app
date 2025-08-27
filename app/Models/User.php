<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'profile_picture',
        'is_active',
        'role_onboarded_at',
        'current_account_id', // Track the currently active account for the user
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'role_onboarded_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function organizerProfile()
    {
        return $this->hasOne(OrganizerProfile::class, 'user_id');
    }

    public function hotelBookings()
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function tourBookings()
    {
        return $this->hasMany(TourBooking::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }

    public function arProgress()
    {
        return $this->hasMany(UserArProgress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Relationships
    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_users')
            ->withPivot(['role', 'permissions', 'is_primary_contact'])
            ->withTimestamps();
    }

    public function currentAccount()
    {
        if (!$this->current_account_id) {
            $this->switchToFirstAccount();
        }
        
        return $this->belongsTo(Account::class, 'current_account_id');
    }

    public function accountRoles()
    {
        return $this->hasMany(AccountUser::class);
    }

    // Account Management
    public function ownsAccount(Account $account): bool
    {
        return $this->accounts()
            ->where('accounts.id', $account->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    public function hasAccountAccess(Account $account): bool
    {
        return $this->accounts()->where('accounts.id', $account->id)->exists();
    }

    public function switchToAccount(Account $account): bool
    {
        if (!$this->hasAccountAccess($account)) {
            return false;
        }

        $this->current_account_id = $account->id;
        return $this->save();
    }

    public function switchToFirstAccount(): bool
    {
        $account = $this->accounts()->first();
        
        if (!$account) {
            return false;
        }
        
        return $this->switchToAccount($account);
    }

    public function getCurrentAccountRole(): ?string
    {
        if (!$this->current_account_id) {
            return null;
        }
        
        return $this->accounts()
            ->where('accounts.id', $this->current_account_id)
            ->first()
            ?->pivot
            ->role;
    }

    public function hasAccountPermission(string $permission, ?Account $account = null): bool
    {
        $account = $account ?: $this->currentAccount;
        
        if (!$account) {
            return false;
        }
        
        $accountUser = $this->accounts()
            ->where('accounts.id', $account->id)
            ->first();
            
        if (!$accountUser) {
            return false;
        }
        
        // If user is owner, they have all permissions
        if ($accountUser->pivot->role === 'owner') {
            return true;
        }
        
        $permissions = $accountUser->pivot->permissions ?? [];
        
        return in_array($permission, $permissions);
    }

    // Role helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuide()
    {
        return $this->role === 'guide';
    }

    public function isRestaurant()
    {
        return $this->role === 'restaurant';
    }

    // Breeze nav expects a name
    public function getNameAttribute()
    {
        $full = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
        return $full !== '' ? $full : ($this->attributes['email'] ?? '');
    }
}
