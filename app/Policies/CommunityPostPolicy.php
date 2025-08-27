<?php

namespace App\Policies;

use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommunityPostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tous les utilisateurs connectés peuvent voir les publications
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CommunityPost $communityPost): bool
    {
        // Tous les utilisateurs connectés peuvent voir une publication spécifique
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs connectés peuvent créer des publications
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CommunityPost $communityPost): bool
    {
        // Seul l'auteur de la publication peut la mettre à jour
        return $user->id === $communityPost->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CommunityPost $communityPost): bool
    {
        // L'auteur de la publication ou un administrateur peut la supprimer
        return $user->id === $communityPost->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CommunityPost $communityPost): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CommunityPost $communityPost): bool
    {
        return false;
    }
}
