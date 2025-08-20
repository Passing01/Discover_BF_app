<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','active']);
    }

    public function index(Request $request)
    {
        // Only bookings for hotels managed by current user
        $q = HotelBooking::with(['room.hotel','user'])
            ->whereHas('room.hotel', function($qq) {
                $qq->where('manager_id', Auth::id());
            });

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }
        if ($request->filled('hotel_id')) {
            $q->whereHas('room.hotel', function($qq) use ($request) {
                $qq->where('id', $request->input('hotel_id'));
            });
        }
        if ($request->filled('from')) {
            $q->whereDate('start_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('end_date', '<=', $request->input('to'));
        }

        $bookings = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('agency.bookings.index', compact('bookings'));
    }

    public function show(HotelBooking $booking)
    {
        abort_unless($booking->room && $booking->room->hotel && $booking->room->hotel->manager_id === Auth::id() || (Auth::user() && Auth::user()->isAdmin()), 403);
        $booking->load(['room.hotel','user']);
        return view('agency.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, HotelBooking $booking)
    {
        abort_unless($booking->room && $booking->room->hotel && $booking->room->hotel->manager_id === Auth::id() || (Auth::user() && Auth::user()->isAdmin()), 403);
        $data = $request->validate([
            'action' => ['required','in:confirm,cancel,checkin,checkout']
        ]);
        $action = $data['action'];
        $allowed = [
            'confirm' => ['pending'],
            'cancel' => ['pending','confirmed'],
            'checkin' => ['confirmed'],
            'checkout' => ['checked_in'],
        ];
        $map = [
            'confirm' => 'confirmed',
            'cancel' => 'cancelled',
            'checkin' => 'checked_in',
            'checkout' => 'checked_out',
        ];
        if (!in_array($booking->status, $allowed[$action] ?? [])) {
            return back()->with('status', "Transition non autorisée depuis le statut {$booking->status}.");
        }
        $booking->status = $map[$action];
        $booking->save();
        return back()->with('status', 'Statut mis à jour.');
    }
}
