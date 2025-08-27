<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityLike extends Model
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
        'post_id',
        'reaction',
    ];

    /**
     * Désactive les timestamps car nous n'en avons pas besoin pour les likes.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relation avec l'utilisateur qui a aimé le post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le post qui a été aimé.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }
}
