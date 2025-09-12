<?php

namespace App\Http\Controllers\HotelManager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Policies\HotelCustomerPolicy;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Hotel::class, 'hotel', [
            'except' => ['index', 'show'],
        ]);
    }
    /**
     * Afficher la liste des clients de l'hôtel
     *
     * @param  \App\Models\Hotel  $hotel
     * @return \Illuminate\View\View
     */
    public function index(Hotel $hotel)
    {
        // Vérifier que l'utilisateur est bien le gestionnaire de cet hôtel
        $this->authorize('viewCustomers', $hotel);

        // Récupérer les réservations groupées par client
        $customers = $hotel->bookings()
            ->with(['user', 'room'])
            ->select('user_id')
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(total_amount) as total_spent')
            ->selectRaw('MAX(created_at) as last_booking_date')
            ->groupBy('user_id')
            ->with(['user' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'email', 'phone', 'country');
            }])
            ->paginate(15);

        return view('hotel-manager.customers.index', compact('hotel', 'customers'));
    }

    /**
     * Afficher les détails d'un client spécifique
     *
     * @param  \App\Models\Hotel  $hotel
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function show(Hotel $hotel, User $user)
    {
        // Vérifier que l'utilisateur est bien le gestionnaire de cet hôtel
        $this->authorize('viewCustomer', $hotel);

        // Récupérer les informations du client
        $customer = $user;

        // Récupérer l'historique des réservations de ce client dans cet hôtel
        $bookings = $hotel->bookings()
            ->with(['room.roomType', 'reviews'])
            ->where('user_id', $user->id)
            ->orderBy('check_in', 'desc')
            ->paginate(10);

        // Calculer les statistiques du client
        $stats = [
            'total_bookings' => $bookings->total(),
            'total_nights' => $bookings->sum('nights'),
            'total_spent' => $bookings->sum('total_amount'),
            'average_rating' => $bookings->avg('reviews.rating')
        ];

        return view('hotel-manager.customers.show', compact('hotel', 'customer', 'bookings', 'stats'));
    }
}
