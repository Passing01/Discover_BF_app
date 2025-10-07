<?php

namespace App\Http\Controllers\RestaurantManager;

use App\Http\Controllers\Controller;
use App\Models\RestaurantReservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        // Log de l'utilisateur connecté
        \Log::info('Utilisateur connecté pour la gestion des réservations', [
            'user_id' => Auth::id(),
            'is_admin' => Auth::user() ? Auth::user()->hasRole('admin') : false,
            'is_restaurant_owner' => Auth::user() ? Auth::user()->hasRole('restaurant_owner') : false
        ]);

        // Récupération des restaurants de l'utilisateur
        $restaurants = Restaurant::where('owner_id', Auth::id())->get();
        
        // Log des restaurants trouvés
        \Log::info('Restaurants trouvés pour l\'utilisateur', [
            'count' => $restaurants->count(),
            'restaurants' => $restaurants->pluck('id', 'name')->toArray()
        ]);

        if ($restaurants->isEmpty()) {
            \Log::warning('Aucun restaurant trouvé pour l\'utilisateur', ['user_id' => Auth::id()]);
            // Créer une pagination manuelle vide
            $reservations = new \Illuminate\Pagination\LengthAwarePaginator(
                [], // Les éléments vides
                0,  // Total des éléments
                15, // Nombre d'éléments par page
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
            return view('restaurant_manager.reservations.index', compact('reservations'));
        }

        // Récupération des réservations
        $reservations = RestaurantReservation::whereIn('restaurant_id', $restaurants->pluck('id'))
            ->with(['restaurant', 'user'])
            ->latest()
            ->paginate(15);

        // Log des réservations trouvées
        \Log::info('Réservations trouvées', [
            'count' => $reservations->total(),
            'restaurant_ids' => $restaurants->pluck('id')->toArray(),
            'first_page_reservations' => $reservations->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'restaurant_id' => $reservation->restaurant_id,
                    'restaurant_name' => $reservation->restaurant ? $reservation->restaurant->name : 'N/A',
                    'user_name' => $reservation->user ? $reservation->user->name : 'N/A',
                    'reservation_at' => $reservation->reservation_at,
                    'status' => $reservation->status
                ];
            })
        ]);

        return view('restaurant_manager.reservations.index', compact('reservations'));
    }

    public function show(RestaurantReservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['restaurant', 'user']);
        return view('restaurant_manager.reservations.show', compact('reservation'));
    }

    public function updateStatus(Request $request, RestaurantReservation $reservation)
    {
        $this->authorize('update', $reservation);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $reservation->update(['status' => $validated['status']]);

        return back()->with('success', 'Statut de la réservation mis à jour');
    }
}
