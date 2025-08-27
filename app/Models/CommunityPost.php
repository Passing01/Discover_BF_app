<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CommunityPost extends Model
{
    /**
     * Indique si le modèle utilise un identifiant incrémenté.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'content',
        'image',
        'is_active',
        'deleted_by',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a créé le post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les commentaires du post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(CommunityComment::class, 'post_id')->latest();
    }
    
    /**
     * Relation avec les likes du post.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommunityLike::class, 'post_id');
    }
    
    /**
     * Relation avec les utilisateurs qui ont aimé le post.
     */
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'community_likes', 'post_id', 'user_id')
            ->withTimestamps();
    }
    
    /**
     * Relation avec l'utilisateur qui a supprimé la publication
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    
    /**
     * Scope pour les publications actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Désactiver la publication
     */
    public function deactivate($userId = null)
    {
        $this->update([
            'is_active' => false,
            'deleted_by' => $userId ?? auth()->id(),
        ]);
        
        return $this;
    }
    
    /**
     * Réactiver la publication
     */
    public function activate()
    {
        $this->update([
            'is_active' => true,
            'deleted_by' => null,
        ]);
        
        return $this;
    }

    /**
     * Vérifie si un utilisateur a aimé ce post.
     *
     * @param int $userId
     * @return bool
     */
    public function isLikedBy($userId): bool
    {
        if (!$userId) {
            return false;
        }
        
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Vérifie si un utilisateur a aimé le post.
     */
    public function isLikedByUser(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Obtenir le nombre de commentaires.
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }

    /**
     * Obtenir le nombre de likes.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
