<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EventBooking;
use App\Models\HotelBooking;
use App\Models\BusBooking;
use App\Models\FlightBooking;
use App\Models\TourBooking;
use App\Models\Ride;

class TouristCalendarController extends Controller
{
    public function index()
    {
        return view('tourist.calendar.index');
    }

    public function feed(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json([]);

        $events = [];

        // Event bookings
        $eventBookings = EventBooking::with('event')
            ->where('user_id', $user->id)
            ->get();
        foreach ($eventBookings as $b) {
            if (!$b->event) continue;
            $events[] = [
                'id' => 'ev_'.$b->id,
                'title' => 'Évènement: '.$b->event->name,
                'start' => (string) $b->event->start_date,
                'end' => (string) $b->event->end_date,
                'allDay' => true,
                'color' => '#2563eb',
                'url' => route('bookings.show', $b),
            ];
        }

        // Hotel bookings (multi-day)
        $hotelBookings = HotelBooking::with('room.hotel')
            ->where('user_id', $user->id)
            ->get();
        foreach ($hotelBookings as $b) {
            $hotelName = optional(optional($b->room)->hotel)->name ?: 'Séjour';
            // FullCalendar expects exclusive end for allDay ranges; add +1 day
            $exclusiveEnd = \Carbon\Carbon::parse($b->end_date)->addDay()->toDateString();
            $events[] = [
                'id' => 'ho_'.$b->id,
                'title' => 'Hôtel: '.$hotelName,
                'start' => (string) $b->start_date,
                'end' => $exclusiveEnd,
                'allDay' => true,
                'color' => '#16a34a',
                'url' => route('tourist.bookings.show', $b->id),
            ];
        }

        // Bus bookings (timed)
        $busBookings = BusBooking::with('trip')
            ->where('user_id', $user->id)
            ->get();
        foreach ($busBookings as $b) {
            if (!$b->trip) continue;
            $events[] = [
                'id' => 'bus_'.$b->id,
                'title' => 'Bus: '.$b->trip->origin.' → '.$b->trip->destination,
                'start' => (string) $b->trip->departure_time,
                'end' => (string) $b->trip->arrival_time,
                'allDay' => false,
                'color' => '#f59e0b',
                'url' => route('transport.bus.booking.show', $b),
            ];
        }

        // Flight bookings (timed)
        $flightBookings = FlightBooking::with('flight.origin','flight.destination')
            ->where('user_id', $user->id)
            ->get();
        foreach ($flightBookings as $b) {
            if (!$b->flight) continue;
            $title = 'Vol: '.($b->flight->origin->iata_code ?? 'Origine').' → '.($b->flight->destination->iata_code ?? 'Destination');
            $events[] = [
                'id' => 'fl_'.$b->id,
                'title' => $title,
                'start' => (string) $b->flight->departure_time,
                'end' => (string) $b->flight->arrival_time,
                'allDay' => false,
                'color' => '#0ea5e9',
                'url' => route('air.bookings.show', $b->id),
            ];
        }

        // Tour bookings (sites) — often a single day
        $tourBookings = TourBooking::with('tour')
            ->where('user_id', $user->id)
            ->get();
        foreach ($tourBookings as $b) {
            $events[] = [
                'id' => 'tour_'.$b->id,
                'title' => 'Visite: '.(optional($b->tour)->title ?? 'Site touristique'),
                'start' => (string) $b->booking_date,
                'allDay' => true,
                'color' => '#10b981',
            ];
        }

        // Taxi rides
        $rides = Ride::with('taxi')
            ->where('user_id', $user->id)
            ->get();
        foreach ($rides as $r) {
            $label = 'Taxi: '.(($r->pickup_location && $r->dropoff_location) ? ($r->pickup_location.' → '.$r->dropoff_location) : 'Course');
            $events[] = [
                'id' => 'ride_'.$r->id,
                'title' => $label,
                'start' => (string) $r->ride_date,
                'allDay' => false,
                'color' => '#ef4444',
                'url' => route('transport.taxi.ride.show', $r->id),
            ];
        }

        // Assistant-generated itinerary from session
        $plan = session('assistant_plan');
        if (is_array($plan) && !empty($plan['itinerary'])) {
            foreach ($plan['itinerary'] as $date => $items) {
                foreach ((array)$items as $idx => $it) {
                    $events[] = [
                        'id' => 'ai_'.md5($date.'_'.$idx.'_'.($it['title'] ?? '')),
                        'title' => '[IA] '.($it['title'] ?? 'Activité'),
                        'start' => $date.(isset($it['time']) && $it['time'] ? 'T'.date('H:i:s', strtotime($it['time'])) : ''),
                        'allDay' => empty($it['time']),
                        'color' => '#9333ea',
                    ];
                }
            }
        }

        // Assistant custom items (fallback to added_at date)
        $custom = session('assistant_custom', []);
        foreach ((array)$custom as $c) {
            $events[] = [
                'id' => 'aic_'.md5(($c['label'] ?? '').'_'.($c['added_at'] ?? '')),
                'title' => '[IA] '.($c['label'] ?? 'Ajout'),
                'start' => (string) ($c['date'] ?? ($c['added_at'] ?? now()->toDateString())),
                'allDay' => true,
                'color' => '#a855f7',
            ];
        }

        return response()->json($events);
    }
}
