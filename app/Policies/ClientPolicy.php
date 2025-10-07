<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel client de l'hôtel.
     */
    public function viewAny(User $user, Hotel $hotel)
    {
        return $user->id === $hotel->manager_id || $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut créer un client pour l'hôtel.
     */
    public function create(User $user, Hotel $hotel)
    {
        return $user->id === $hotel->manager_id || $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut voir un client spécifique de l'hôtel.
     */
    public function view(User $user, User $client, Hotel $hotel)
    {
        // Vérifier que le client a effectué des réservations dans cet hôtel
        $hasBookings = $client->hotelBookings()
            ->whereHas('room', function($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->exists();
            
        return ($user->id === $hotel->manager_id || $user->hasRole('admin')) && $hasBookings;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un client spécifique de l'hôtel.
     */
    public function update(User $user, User $client, Hotel $hotel)
    {
        // Vérifier que le client a effectué des réservations dans cet hôtel
        $hasBookings = $client->hotelBookings()
            ->whereHas('room', function($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->exists();
            
        return ($user->id === $hotel->manager_id || $user->hasRole('admin')) && $hasBookings;
    }
}
