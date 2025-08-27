<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'parent_id' => 'integer'
    ];

    /**
     * Obtenir la catégorie parente
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'parent_id');
    }

    /**
     * Obtenir les sous-catégories
     */
    public function children(): HasMany
    {
        return $this->hasMany(EventCategory::class, 'parent_id')->orderBy('order');
    }

    /**
     * Vérifier si la catégorie a des sous-catégories
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Obtenir les événements de cette catégorie
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    /**
     * Obtenir uniquement les catégories parentes
     */
    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Obtenir les catégories actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Générer un slug à partir du nom
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function($category) {
            $category->slug = \Illuminate\Support\Str::slug($category->name);
        });
    }
}
