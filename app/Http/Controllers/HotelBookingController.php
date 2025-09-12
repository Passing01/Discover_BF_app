<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function index()
    {
        $hotelIds = auth()->user()->hotels()->pluck('id');
        
        $bookings = HotelBooking::with(['room.hotel', 'user'])
            ->whereHas('room', function($query) use ($hotelIds) {
                $query->whereIn('hotel_id', $hotelIds);
            })
            ->latest()
            ->paginate(15);
            
        return view('hotel-manager.bookings.index', compact('bookings'));
    }
}
