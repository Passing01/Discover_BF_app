<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Carbon;

class Ad extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'placement','title','image_path','target_url','cta_text','starts_at','ends_at','enabled','weight'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'weight' => 'integer',
    ];

    public function scopeActiveFor($query, string $placement)
    {
        $now = Carbon::now();
        return $query->where('placement', $placement)
            ->where('enabled', true)
            ->where(function($q) use ($now){
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now){
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
