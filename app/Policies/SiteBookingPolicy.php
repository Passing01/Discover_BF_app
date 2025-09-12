<?php

namespace App\Policies;

use App\Models\SiteBooking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SiteBookingPolicy
{
    /**
     * Vérifie si l'utilisateur peut voir n'importe quelle réservation
     */
    public function viewAny(User $user): bool
    {
        // Les administrateurs et les gestionnaires de sites peuvent voir toutes les réservations
        return $user->hasRole('admin') || $user->hasRole('site_manager');
    }

    /**
     * Vérifie si l'utilisateur peut voir une réservation spécifique
     */
    public function view(User $user, SiteBooking $booking): bool
    {
        // Un administrateur peut tout voir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites ne peut voir que les réservations de ses sites
        if ($user->hasRole('site_manager')) {
            return $booking->site->manager_id === $user->id;
        }
        
        // Un utilisateur ne peut voir que ses propres réservations
        return $booking->user_id === $user->id;
    }

    /**
     * Vérifie si l'utilisateur peut créer une réservation
     */
    public function create(User $user): bool
    {
        // Seuls les utilisateurs authentifiés peuvent créer des réservations
        return $user->hasRole('user') || $user->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour une réservation
     */
    public function update(User $user, SiteBooking $booking): bool
    {
        // Un administrateur peut tout modifier
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites peut mettre à jour les réservations de ses sites
        if ($user->hasRole('site_manager')) {
            return $booking->site->manager_id === $user->id;
        }
        
        // Un utilisateur ne peut modifier que ses propres réservations, et seulement si elles sont en attente
        return $booking->user_id === $user->id && $booking->status === 'pending';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une réservation
     */
    public function delete(User $user, SiteBooking $booking): bool
    {
        // Un administrateur peut tout supprimer
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites peut supprimer les réservations de ses sites
        if ($user->hasRole('site_manager')) {
            return $booking->site->manager_id === $user->id;
        }
        
        // Un utilisateur ne peut supprimer que ses propres réservations, et seulement si elles sont en attente
        return $booking->user_id === $user->id && $booking->status === 'pending';
    }

    /**
     * Vérifie si l'utilisateur peut restaurer une réservation supprimée
     */
    public function restore(User $user, SiteBooking $booking): bool
    {
        // Seuls les administrateurs peuvent restaurer les réservations supprimées
        return $user->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur peut supprimer définitivement une réservation
     */
    public function forceDelete(User $user, SiteBooking $booking): bool
    {
        // Seuls les administrateurs peuvent supprimer définitivement les réservations
        return $user->hasRole('admin');
    }
    
    /**
     * Vérifie si l'utilisateur peut mettre à jour le statut d'une réservation
     */
    public function updateStatus(User $user, SiteBooking $booking): bool
    {
        // Un administrateur peut tout faire
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites peut mettre à jour le statut des réservations de ses sites
        if ($user->hasRole('site_manager')) {
            return $booking->site->manager_id === $user->id;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut annuler une réservation
     */
    public function cancel(User $user, SiteBooking $booking): bool
    {
        // Un administrateur peut tout annuler
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Un gestionnaire de sites peut annuler les réservations de ses sites
        if ($user->hasRole('site_manager')) {
            return $booking->site->manager_id === $user->id;
        }
        
        // Un utilisateur ne peut annuler que ses propres réservations, et seulement si elles sont en attente ou confirmées
        return $booking->user_id === $user->id && 
               in_array($booking->status, ['pending', 'confirmed']);
    }
}
