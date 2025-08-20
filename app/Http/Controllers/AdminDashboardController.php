<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventBooking;
use App\Models\Payment;
use App\Models\RestaurantReservation;
use App\Models\DishOrder;
use App\Models\FlightBooking;
use App\Models\BusBooking;
use App\Models\Ride;
use App\Models\Restaurant;
use App\Models\Hotel;
use App\Models\Taxi;
use App\Models\Bus;
use App\Models\BusTrip;
use App\Models\Flight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // High-level KPI blocks
        $metrics = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'events' => [
                'total' => Event::count(),
                'bookings' => EventBooking::count(),
            ],
            'commerce' => [
                'payments_total' => (float) Payment::where('status', 'paid')->sum('amount'),
                'restaurant_reservations' => RestaurantReservation::count(),
                'dish_orders' => DishOrder::count(),
            ],
            'transport' => [
                'flight_bookings' => FlightBooking::count(),
                'bus_bookings' => BusBooking::count(),
                'rides' => Ride::count(),
            ],
        ];

        // Role distribution counts
        $roleCounts = User::select('role', DB::raw('COUNT(*) as c'))
            ->groupBy('role')
            ->pluck('c', 'role')
            ->toArray();

        // Entities counts
        $entities = [
            'tourists' => User::where('role', 'tourist')->count(),
            'event_organizers' => User::where('role', 'event_organizer')->count(),
            'hotel_managers' => User::where('role', 'hotel_manager')->count(),
            'guides' => User::where('role', 'guide')->count(),
            'drivers' => User::where('role', 'driver')->count(),
            'restaurants' => Restaurant::count(),
            'hotels' => Hotel::count(),
            'taxis' => Taxi::count(),
            'buses' => Bus::count(),
            'bus_trips' => BusTrip::count(),
            'flights' => Flight::count(),
            'events' => Event::count(),
        ];

        // Prepare charts datasets
        $rolesChart = [
            'labels' => array_values(array_keys($roleCounts)),
            'data' => array_values(array_values($roleCounts)),
        ];

        $entitiesChart = [
            'labels' => [
                'Touristes','Organisateurs','Gestionnaires hôtel','Guides','Chauffeurs',
                'Restaurants','Hôtels','Taxis','Bus','Trajets Bus','Vols','Événements'
            ],
            'data' => [
                $entities['tourists'],
                $entities['event_organizers'],
                $entities['hotel_managers'],
                $entities['guides'],
                $entities['drivers'],
                $entities['restaurants'],
                $entities['hotels'],
                $entities['taxis'],
                $entities['buses'],
                $entities['bus_trips'],
                $entities['flights'],
                $entities['events'],
            ],
        ];

        // Time range (period) for charts
        $period = (int) request('period', 30);
        if (!in_array($period, [7, 30, 90], true)) { $period = 30; }
        $end = Carbon::now()->endOfDay();
        $start = (clone $end)->subDays($period - 1)->startOfDay();

        // Build date keys map for zero-filling
        $dateKeys = [];
        for ($d = (clone $start); $d <= $end; $d->addDay()) {
            $dateKeys[$d->format('Y-m-d')] = [ 'payments' => 0.0, 'bookings' => 0 ];
        }

        // Payments per day (paid)
        $payments = Payment::select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(amount) as s'))
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('s', 'd')
            ->toArray();

        // Bookings per day across domains
        $aggCounts = function($model) use ($start, $end) {
            return $model::select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('c', 'd')
                ->toArray();
        };

        $bookingsMaps = [
            $aggCounts(\App\Models\EventBooking::class),
            $aggCounts(\App\Models\FlightBooking::class),
            $aggCounts(\App\Models\BusBooking::class),
            $aggCounts(\App\Models\Ride::class),
            $aggCounts(\App\Models\RestaurantReservation::class),
        ];

        // Merge into dateKeys
        foreach ($dateKeys as $d => $vals) {
            if (isset($payments[$d])) { $dateKeys[$d]['payments'] = (float) $payments[$d]; }
            foreach ($bookingsMaps as $map) {
                if (isset($map[$d])) { $dateKeys[$d]['bookings'] += (int) $map[$d]; }
            }
        }

        $timeseries = [
            'labels' => array_keys($dateKeys),
            'payments' => array_map(fn($v) => round($v['payments'], 2), array_values($dateKeys)),
            'bookings' => array_map(fn($v) => $v['bookings'], array_values($dateKeys)),
            'period' => $period,
        ];

        $recentUsers = User::orderByDesc('created_at')->limit(6)->get();
        $recentEvents = Event::orderByDesc('created_at')->limit(6)->get();

        return view('admin.dashboard', compact('metrics', 'recentUsers', 'recentEvents', 'rolesChart', 'entitiesChart', 'entities', 'timeseries'));
    }
}
