<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\RestaurantReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantReservationController extends Controller
{
    /**
     * Lister les réservations de l'utilisateur connecté (protégé Sanctum).
     */
    public function index(Request $request)
    {
        $reservations = RestaurantReservation::with('restaurant')
            ->where('user_id', Auth::id())
            ->orderByDesc('reservation_at')
            ->paginate(10);

        return RestaurantReservationResource::collection($reservations);
    }

    /**
     * Afficher une réservation spécifique appartenant à l'utilisateur (protégé Sanctum).
     */
    public function show(RestaurantReservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load('restaurant');

        return new RestaurantReservationResource($reservation);
    }
}
