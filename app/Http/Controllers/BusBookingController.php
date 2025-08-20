<?php

namespace App\Http\Controllers;

use App\Models\BusTrip;
use App\Models\BusBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusBookingController extends Controller
{
    public function create(BusTrip $trip)
    {
        return view('transport.bus.book', compact('trip'));
    }

    public function store(Request $request, BusTrip $trip)
    {
        $data = $request->validate([
            'seats' => ['required','integer','min:1'],
            'class_option' => ['nullable','in:standard,comfort,vip'],
        ]);

        // Restrict booking to tourists only
        if (Auth::check()) {
            $role = Auth::user()->role ?? 'tourist';
            if (in_array($role, ['hotel_manager','guide','restaurant_manager','admin'])) {
                return back()->withErrors([
                    'seats' => __('Seuls les touristes peuvent réserver des bus.'),
                ])->withInput();
            }
        }

        $multipliers = [
            'standard' => 1,
            'comfort' => 1.2,
            'vip' => 1.5,
        ];

        $selectedClass = $data['class_option'] ?? 'standard';
        $multiplier = $multipliers[$selectedClass] ?? 1;

        return DB::transaction(function () use ($trip, $data, $multiplier) {
            if ($trip->seats_available < $data['seats']) {
                return back()->withErrors(['seats' => __('Pas assez de places disponibles')]);
            }

            // total = base_price * multiplier * seats
            $base = bcmul((string)$trip->price, (string)$multiplier, 2);
            $total = bcmul($base, (string)$data['seats'], 2);

            $booking = BusBooking::create([
                'user_id' => Auth::id(),
                'bus_trip_id' => $trip->id,
                'seats' => $data['seats'],
                'total_price' => $total,
                'status' => 'pending',
            ]);

            $trip->decrement('seats_available', $data['seats']);

            return redirect()->route('transport.bus.booking.show', $booking);
        });
    }

    public function show(BusBooking $booking)
    {
        if (!Auth::check() || $booking->user_id !== Auth::id()) {
            abort(403);
        }
        return view('transport.bus.booking_show', compact('booking'));
    }
}
