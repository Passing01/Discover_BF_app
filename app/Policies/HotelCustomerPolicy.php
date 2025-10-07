<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HotelCustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent voir la liste des hôtels
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Hotel $hotel): bool
    {
        // Seul le gestionnaire de l'hôtel peut voir les détails d'un hôtel
        return $user->id === $hotel->manager_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Seuls les utilisateurs avec le rôle hotel_manager peuvent créer des hôtels
        return $user->role === 'hotel_manager';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Hotel $hotel): bool
    {
        // Seul le gestionnaire de l'hôtel peut le mettre à jour
        return $user->id === $hotel->manager_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Hotel $hotel): bool
    {
        // Seul le gestionnaire de l'hôtel peut le supprimer
        return $user->id === $hotel->manager_id;
    }

    /**
     * Determine whether the user can view the hotel's customers.
     */
    public function viewCustomers(User $user, Hotel $hotel): bool
    {
        // Seul le gestionnaire de l'hôtel peut voir la liste des clients
        return $user->id === $hotel->manager_id;
    }

    /**
     * Determine whether the user can view a specific customer's details.
     */
    public function viewCustomer(User $user, Hotel $hotel): bool
    {
        // Seul le gestionnaire de l'hôtel peut voir les détails d'un client
        return $user->id === $hotel->manager_id;
    }
}
