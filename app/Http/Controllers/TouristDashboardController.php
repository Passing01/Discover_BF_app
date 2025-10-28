<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;
use App\Models\Flight;
use App\Models\Taxi;

class TouristDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Basic datasets (safe fallbacks)
        $today = now()->toDateString();
        $itinerary = session('itinerary');
        $currentTrip = null;
        $todayItems = [];

        if (is_array($itinerary)) {
            $currentTrip = [
                'start' => $itinerary['start_date'] ?? null,
                'end' => $itinerary['end_date'] ?? null,
                'city' => $itinerary['days'][0]['city'] ?? null,
                'days' => $itinerary['days'] ?? [],
            ];
            // pick items matching today
            foreach (($itinerary['days'] ?? []) as $d) {
                if (!empty($d['date']) && $d['date'] === $today) {
                    $todayItems[] = $d;
                }
            }
            // if none exactly today, propose first day as preview
            if (empty($todayItems) && !empty($itinerary['days'])) {
                $todayItems[] = $itinerary['days'][0];
            }
        }
        // Pick the correct date column based on existing schema (backward compatible)
        $eventDateColumn = null;
        if (Schema::hasColumn('events', 'starts_at')) {
            $eventDateColumn = 'starts_at';
        } elseif (Schema::hasColumn('events', 'start_date')) {
            $eventDateColumn = 'start_date';
        }

        $upcomingEvents = collect();
        if ($eventDateColumn !== null) {
            $upcomingEvents = Event::query()
                ->whereDate($eventDateColumn, '>=', $today)
                ->orderBy($eventDateColumn)
                ->limit(6)
                ->get();
        }

        $flights = Flight::query()->orderBy('departure_time')->limit(3)->get();
        $taxis = Taxi::query()->limit(3)->get();

        return view('tourist.dashboard', [
            'user' => $user,
            'events' => $upcomingEvents,
            'flights' => $flights,
            'taxis' => $taxis,
            'today' => $today,
            'currentTrip' => $currentTrip,
            'todayItems' => $todayItems,
        ]);
    }
}
