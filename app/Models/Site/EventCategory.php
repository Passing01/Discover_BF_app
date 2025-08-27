<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCategory extends Model
{
    use SoftDeletes;
    
    protected $table = 'site_event_categories';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'icon',
        'color',
        'parent_id',
        'order',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeParent($query, $parentId = null)
    {
        if ($parentId === null) {
            return $query->whereNull('parent_id');
        }
        
        return $query->where('parent_id', $parentId);
    }
    
    // Relations
    public function parent()
    {
        return $this->belongsTo(EventCategory::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(EventCategory::class, 'parent_id')->orderBy('order');
    }
    
    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }
    
    // Methods
    public function getBreadcrumb()
    {
        $breadcrumbs = [];
        $category = $this;
        
        while ($category) {
            $breadcrumbs[] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
            $category = $category->parent;
        }
        
        return array_reverse($breadcrumbs);
    }
    
    public function getFullPath()
    {
        $path = [];
        $category = $this;
        
        while ($category) {
            array_unshift($path, $category->slug);
            $category = $category->parent;
        }
        
        return implode('/', $path);
    }
    
    public function getEventsCount()
    {
        return $this->events()->count();
    }
    
    public function hasChildren()
    {
        return $this->children()->exists();
    }
    
    public function isChildOf($categoryId)
    {
        $category = $this;
        
        while ($category->parent) {
            if ($category->parent_id === $categoryId) {
                return true;
            }
            $category = $category->parent;
        }
        
        return false;
    }
    
    public function getIconHtml()
    {
        if (empty($this->icon)) {
            return '<i class="fas fa-calendar-alt"></i>';
        }
        
        if (strpos($this->icon, 'fa-') === 0) {
            return '<i class="fas ' . $this->icon . '"></i>';
        }
        
        if (filter_var($this->icon, FILTER_VALIDATE_URL)) {
            return '<img src="' . $this->icon . '" alt="" class="category-icon">';
        }
        
        return $this->icon;
    }
}
