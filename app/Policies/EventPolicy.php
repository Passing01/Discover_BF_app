<?php

namespace App\Policies;

use App\Models\Site\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel événement.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('site_manager');
    }

    /**
     * Détermine si l'utilisateur peut voir un événement spécifique.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->hasRole('site_manager') && $event->site_id === $user->site->id;
    }

    /**
     * Détermine si l'utilisateur peut créer des événements.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('site_manager');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un événement.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->hasRole('site_manager') && $event->site_id === $user->site->id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un événement.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->hasRole('site_manager') && $event->site_id === $user->site->id;
    }

    /**
     * Détermine si l'utilisateur peut restaurer un événement supprimé.
     */
    public function restore(User $user, Event $event): bool
    {
        return $user->hasRole('site_manager') && $event->site_id === $user->site->id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement un événement.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $user->hasRole('admin'); // Seul l'admin peut supprimer définitivement
    }

    /**
     * Détermine si l'utilisateur peut gérer les inscriptions à un événement.
     */
    public function manageRegistrations(User $user, Event $event): bool
    {
        return $user->hasRole('site_manager') && $event->site_id === $user->site->id;
    }
}
