<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StayRule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rules';
    protected $fillable = ['name'];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_rule', 'rule_id', 'hotel_id');
    }
}
