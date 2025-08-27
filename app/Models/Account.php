<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'business_name',
        'legal_name',
        'tax_number',
        'registration_number',
        'website',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'timezone',
        'currency',
        'logo_path',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'account_users')
            ->withPivot(['role', 'permissions', 'is_primary_contact'])
            ->withTimestamps();
    }

    public function features()
    {
        return $this->hasMany(AccountFeature::class);
    }

    public function verification()
    {
        return $this->hasOne(AccountVerification::class);
    }

    public function billing()
    {
        return $this->hasOne(AccountBilling::class);
    }

    public function hasFeature(string $feature): bool
    {
        return $this->features()
            ->where('feature', $feature)
            ->where('is_active', true)
            ->exists();
    }

    public function getPrimaryContactAttribute()
    {
        return $this->users()
            ->wherePivot('is_primary_contact', true)
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isVerified(): bool
    {
        return $this->status === 'active' && $this->verified_at !== null;
    }
}
