<?php

namespace App\Policies;

use App\Models\CommunityComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommunityCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tous les utilisateurs connectés peuvent voir les commentaires
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CommunityComment $communityComment): bool
    {
        // Tous les utilisateurs connectés peuvent voir un commentaire spécifique
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs connectés peuvent créer des commentaires
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CommunityComment $communityComment): bool
    {
        // Seul l'auteur du commentaire peut le mettre à jour
        return $user->id === $communityComment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CommunityComment $communityComment): bool
    {
        // L'auteur du commentaire ou un administrateur peut le supprimer
        return $user->id === $communityComment->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CommunityComment $communityComment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CommunityComment $communityComment): bool
    {
        return false;
    }
}
