<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking as Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Resources\BookingResource;
use App\Http\Requests\StoreBookingRequest;

class BookingController extends Controller
{
    /**
     * Créer une réservation.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $room = Room::findOrFail($validated['room_id']);
        if (!auth()->check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        if (!$room->available) {
            return response()->json(['error' => 'Room is not available'], 400);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        $room->update(['available' => false]);

        return new BookingResource($booking);
    }

    /**
     * Afficher une réservation spécifique.
     */
    public function show(Booking $booking)
    {
        return new BookingResource($booking);
    }
}