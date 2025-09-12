<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SitePolicy
{
    /**
     * Vérifie si l'utilisateur peut voir n'importe quel site
     */
    public function viewAny(User $user): bool
    {
        // Seuls les administrateurs et les gestionnaires de sites peuvent voir la liste des sites
        return $user->hasRole('admin') || $user->hasRole('site_manager');
    }

    /**
     * Vérifie si l'utilisateur peut voir un site spécifique
     */
    public function view(User $user, Site $site): bool
    {
        // Un administrateur peut tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites ne peut voir que ses propres sites
        if ($user->hasRole('site_manager')) {
            return $site->manager_id === $user->id;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut créer un site
     */
    public function create(User $user): bool
    {
        // Seuls les administrateurs et les gestionnaires de sites peuvent créer des sites
        return $user->hasRole('admin') || $user->hasRole('site_manager');
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour un site
     */
    public function update(User $user, Site $site): bool
    {
        // Un administrateur peut tout modifier
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites ne peut modifier que ses propres sites
        if ($user->hasRole('site_manager')) {
            return $site->manager_id === $user->id;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un site
     */
    public function delete(User $user, Site $site): bool
    {
        // Un administrateur peut tout supprimer
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites ne peut supprimer que ses propres sites
        if ($user->hasRole('site_manager')) {
            return $site->manager_id === $user->id;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut restaurer un site supprimé
     */
    public function restore(User $user, Site $site): bool
    {
        // Seuls les administrateurs peuvent restaurer les sites supprimés
        return $user->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur peut supprimer définitivement un site
     */
    public function forceDelete(User $user, Site $site): bool
    {
        // Seuls les administrateurs peuvent supprimer définitivement les sites
        return $user->hasRole('admin');
    }
    
    /**
     * Vérifie si l'utilisateur peut gérer les réservations d'un site
     */
    public function manageBookings(User $user, Site $site): bool
    {
        // Un administrateur peut tout gérer
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites ne peut gérer que les réservations de ses propres sites
        if ($user->hasRole('site_manager')) {
            return $site->manager_id === $user->id;
        }
        
        return false;
    }
}
