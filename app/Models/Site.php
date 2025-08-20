<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Site extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name','city','category','description','price_min','price_max','latitude','longitude','photo_url'
    ];
}
