<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTemplate extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'primary_color',
        'secondary_color',
        'text_color',
        'bg_image_path',
        'pdf_path',
        'pdf_page_count',
        'overlay_color',
        'overlay_opacity',
        'logo_enabled',
        'logo_position',
        'logo_size',
        'corner_radius',
        'card_shadow_enabled',
        'logo_placement',
        'image_placement',
        'shape',
        'font_family',
        'qr_position',
        'qr_size',
        'layout_json',
        'overlay_fields',
    ];

    protected $casts = [
        'layout_json' => 'array',
        'overlay_fields' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
