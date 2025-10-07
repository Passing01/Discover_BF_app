<?php

namespace App\Policies;

use App\Models\RestaurantReservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('restaurant_owner');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RestaurantReservation $reservation): bool
    {
        return $user->id === $reservation->restaurant->owner_id;
    }

    /**
     * Determine whether the user can update the status of the reservation.
     */
    public function updateStatus(User $user, RestaurantReservation $reservation): bool
    {
        return $user->id === $reservation->restaurant->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RestaurantReservation $reservation): bool
    {
        return $user->id === $reservation->restaurant->owner_id;
    }
}
