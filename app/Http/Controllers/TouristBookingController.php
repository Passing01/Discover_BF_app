<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TouristBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','active']);
    }

    public function index(Request $request)
    {
        // Hotels
        $hotelBookings = \App\Models\HotelBooking::with(['room.hotel'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        // Events
        $eventBookings = \App\Models\EventBooking::with(['event'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        // Bus
        $busBookings = \App\Models\BusBooking::with(['trip'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        // Tours / Sites touristiques
        $tourBookings = \App\Models\TourBooking::with(['tour'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        // Taxi rides
        $rides = \App\Models\Ride::with(['taxi'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        // Restaurants (table)
        $restaurantReservations = \App\Models\RestaurantReservation::with(['restaurant'])
            ->where('user_id', Auth::id())
            ->orderByDesc('reservation_at')
            ->get();

        // Dish delivery orders
        $dishOrders = \App\Models\DishOrder::with(['restaurant','dish'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('tourist.bookings.index', [
            'hotelBookings' => $hotelBookings,
            'eventBookings' => $eventBookings,
            'busBookings' => $busBookings,
            'tourBookings' => $tourBookings,
            'rides' => $rides,
            'restaurantReservations' => $restaurantReservations,
            'dishOrders' => $dishOrders,
        ]);
    }

    public function show(HotelBooking $booking)
    {
        abort_unless($booking->user_id === Auth::id() || (Auth::user() && Auth::user()->isAdmin()), 403);
        $booking->load('room.hotel');
        return view('tourist.bookings.show', compact('booking'));
    }

    public function cancel(HotelBooking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        if (in_array($booking->status, ['cancelled','checked_in','checked_out'])) {
            return back()->with('status', 'Cette réservation ne peut pas être annulée.');
        }
        $booking->status = 'cancelled';
        $booking->save();
        return redirect()->route('tourist.bookings.index')->with('status', 'Réservation annulée.');
    }
}
