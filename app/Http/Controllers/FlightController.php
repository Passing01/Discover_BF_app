<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $airports = Airport::orderBy('city')->get();

        $query = Flight::query()->with(['origin','destination'])
            ->whereHas('destination', function($q){ $q->where('country', 'Burkina Faso'); });

        // filter by airport IDs
        if ($request->filled('origin')) {
            $query->where('origin_airport_id', $request->origin);
        }
        if ($request->filled('destination')) {
            $query->where('destination_airport_id', $request->destination);
        }
        // filter by IATA codes or city
        if ($request->filled('origin_iata')) {
            $code = trim($request->origin_iata);
            $query->whereHas('origin', function($q) use ($code) {
                $q->where('iata_code', 'LIKE', $code)
                  ->orWhere('city', 'LIKE', "%$code%")
                  ->orWhere('name', 'LIKE', "%$code%");
            });
        }
        if ($request->filled('destination_iata')) {
            $code = trim($request->destination_iata);
            $query->whereHas('destination', function($q) use ($code) {
                $q->where('iata_code', 'LIKE', $code)
                  ->orWhere('city', 'LIKE', "%$code%")
                  ->orWhere('name', 'LIKE', "%$code%");
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        $flights = $query->orderBy('departure_time')->paginate(12)->withQueryString();

        return view('air.flights.index', compact('flights','airports'));
    }

    public function show(Flight $flight)
    {
        $flight->load(['origin','destination']);
        return view('air.flights.show', compact('flight'));
    }

    public function book(Flight $flight)
    {
        $flight->load(['origin','destination']);
        return view('air.flights.book', compact('flight'));
    }

    public function storeBooking(Request $request, Flight $flight)
    {
        // Restrict booking to tourists only
        if (Auth::check()) {
            $role = Auth::user()->role ?? 'tourist';
            if (in_array($role, ['hotel_manager','guide','restaurant_manager','admin'])) {
                return back()->withErrors([
                    'adult_count' => __('Seuls les touristes peuvent réserver des vols.'),
                ])->withInput();
            }
        }

        $data = $request->validate([
            'adult_count' => ['required','integer','min:1'],
            'child_count' => ['nullable','integer','min:0'],
            'infant_count' => ['nullable','integer','min:0'],
            'baggage_count' => ['nullable','integer','min:0'],
            'class' => ['required','in:economy,business,first'],
            'contact_name' => ['required','string','max:255'],
            'contact_email' => ['required','email'],
            'contact_phone' => ['nullable','string','max:50'],
            'passengers' => ['nullable','array'],
        ]);

        $adults = (int)($data['adult_count'] ?? 1);
        $children = (int)($data['child_count'] ?? 0);
        $infants = (int)($data['infant_count'] ?? 0);
        $baggage = (int)($data['baggage_count'] ?? 0);

        return DB::transaction(function () use ($flight, $data, $adults, $children, $infants, $baggage) {
            $seatsNeeded = $adults + $children; // infants no seat
            if ($flight->seats_available < $seatsNeeded) {
                return back()->withErrors(['adult_count' => __('Pas assez de sièges disponibles')]);
            }

            $classMultiplier = match($data['class']) {
                'business' => 1.6,
                'first' => 2.2,
                default => 1.0,
            };

            $base = $flight->base_price * $classMultiplier;
            $adultSubtotal = $base * $adults;
            $childSubtotal = $base * 0.75 * $children; // 25% discount
            $infantSubtotal = $base * 0.10 * $infants; // 90% discount, no seat
            $fareSubtotal = $adultSubtotal + $childSubtotal + $infantSubtotal;

            $baggageFee = 5000 * $baggage; // flat per bag
            $taxes = round($fareSubtotal * 0.18, 2); // 18% taxes
            $total = round($fareSubtotal + $baggageFee + $taxes, 2);

            $passengersPayload = $data['passengers'] ?? [];
            $passengersPayload['summary'] = [
                'adults' => $adults,
                'children' => $children,
                'infants' => $infants,
                'baggage' => $baggage,
                'class_multiplier' => $classMultiplier,
                'baggage_fee' => $baggageFee,
                'taxes' => $taxes,
                'fare_subtotal' => round($fareSubtotal, 2),
            ];

            $booking = FlightBooking::create([
                'user_id' => Auth::id(),
                'flight_id' => $flight->id,
                'passengers_count' => $adults + $children + $infants,
                'class' => $data['class'],
                'total_price' => $total,
                'status' => 'pending',
                'contact_name' => $data['contact_name'],
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'] ?? null,
                'passengers' => $passengersPayload,
            ]);

            if ($seatsNeeded > 0) {
                $flight->decrement('seats_available', $seatsNeeded);
            }

            return redirect()->route('air.bookings.show', $booking);
        });
    }

    public function showBooking(FlightBooking $booking)
    {
        if (!Auth::check() || $booking->user_id !== Auth::id()) {
            abort(403);
        }
        $booking->load(['flight.origin','flight.destination']);
        return view('air.flights.booking_show', compact('booking'));
    }

    // --- Wizard pages (mockup-aligned) ---

    public function wizard(Request $request)
    {
        $airports = Airport::orderBy('city')->get();

        $outDate = $request->input('out_date');
        $retDate = $request->input('ret_date');

        $originIata = $request->input('origin_iata');
        $destIata = $request->input('destination_iata', 'OUA');

        $baseQuery = Flight::query()->with(['origin','destination']);
        if ($originIata) {
            $baseQuery->whereHas('origin', function($q) use ($originIata){
                $q->where('iata_code', 'LIKE', $originIata)
                  ->orWhere('city', 'LIKE', "%$originIata%")
                  ->orWhere('name', 'LIKE', "%$originIata%");
            });
        }
        if ($destIata) {
            $baseQuery->whereHas('destination', function($q) use ($destIata){
                $q->where('iata_code', 'LIKE', $destIata)
                  ->orWhere('city', 'LIKE', "%$destIata%")
                  ->orWhere('name', 'LIKE', "%$destIata%");
            });
        }

        $outbound = (clone $baseQuery);
        if ($outDate) { $outbound->whereDate('departure_time', $outDate); }
        $outbound = $outbound->orderBy('departure_time')->limit(10)->get();

        $return = Flight::query()->with(['origin','destination']);
        // For return, invert origin/destination compared to outbound IATAs
        if ($destIata) {
            $return->whereHas('origin', function($q) use ($destIata){
                $q->where('iata_code', 'LIKE', $destIata)
                  ->orWhere('city', 'LIKE', "%$destIata%")
                  ->orWhere('name', 'LIKE', "%$destIata%");
            });
        }
        if ($originIata) {
            $return->whereHas('destination', function($q) use ($originIata){
                $q->where('iata_code', 'LIKE', $originIata)
                  ->orWhere('city', 'LIKE', "%$originIata%")
                  ->orWhere('name', 'LIKE', "%$originIata%");
            });
        }
        if ($retDate) { $return->whereDate('departure_time', $retDate); }
        $return = $return->orderBy('departure_time')->limit(10)->get();

        $selected = session('air_selected', []);

        return view('air.flights.wizard', compact('airports','outbound','return','selected'));
    }

    public function selectLeg(Request $request)
    {
        $data = $request->validate([
            'leg' => ['required','in:outbound,return'],
            'flight_id' => ['required','uuid'],
        ]);
        $flight = Flight::with(['origin','destination'])->findOrFail($data['flight_id']);
        $selected = session('air_selected', []);
        $selected[$data['leg']] = $flight->only(['id','airline','departure_time','arrival_time']);
        $selected[$data['leg']]['summary'] = [
            'route' => ($flight->origin->city ?? '—')." → ".($flight->destination->city ?? '—'),
            'price' => $flight->base_price,
        ];
        session(['air_selected' => $selected]);
        return back()->with('status', __('Sélection mise à jour.'));
    }

    public function details(Request $request)
    {
        $selected = session('air_selected');
        if (!$selected || empty($selected['outbound'])) {
            return redirect()->route('air.flights.wizard')->with('status', __('Veuillez sélectionner au moins un vol aller.'));
        }
        // If user is authenticated, show their saved payment methods (future)
        return view('air.flights.details', compact('selected'));
    }

    public function bookingsIndex()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $bookings = FlightBooking::with(['flight.origin','flight.destination'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(12);
        return view('air.flights.bookings_index', compact('bookings'));
    }

    public function exportBookings()
    {
        if (!Auth::check()) {
            abort(403);
        }
        $data = FlightBooking::with(['flight.origin','flight.destination'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();
        return response()->streamDownload(function() use ($data){
            echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        }, 'bookings-'.date('Ymd-His').'.json', ['Content-Type' => 'application/json; charset=utf-8']);
    }

    public function destroyBooking(FlightBooking $booking)
    {
        if (!Auth::check() || $booking->user_id !== Auth::id()) {
            abort(403);
        }
        $booking->load('flight');

        DB::transaction(function() use ($booking) {
            // Restore the seats that were reserved: adults + children (infants no seat)
            $adults = (int) data_get($booking->passengers, 'summary.adults', 0);
            $children = (int) data_get($booking->passengers, 'summary.children', 0);
            $seatsToRestore = $adults + $children;
            if ($seatsToRestore <= 0 && $booking->passengers_count) {
                // Fallback: if we don't have summary, assume all except 0 infants
                $seatsToRestore = max(0, (int) $booking->passengers_count);
            }
            if ($seatsToRestore > 0 && $booking->flight) {
                $booking->flight->increment('seats_available', $seatsToRestore);
            }

            $booking->delete();
        });

        return redirect()->route('air.bookings.index')->with('status', __('Réservation supprimée.'));
    }
}
