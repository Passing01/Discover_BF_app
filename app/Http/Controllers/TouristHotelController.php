<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\HotelBooking;
use App\Models\Amenity;
use App\Models\StayRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TouristHotelController extends Controller
{
    public function index(Request $request)
    {
        $q = Hotel::query()
            ->withCount('rooms')
            ->withMin('rooms', 'price_per_night');

        // Text search across name, city, country, description
        if ($request->filled('q')) {
            $term = $request->string('q');
            $q->where(function($qq) use ($term) {
                $qq->where('name', 'like', '%'.$term.'%')
                   ->orWhere('city', 'like', '%'.$term.'%')
                   ->orWhere('country', 'like', '%'.$term.'%')
                   ->orWhere('description', 'like', '%'.$term.'%');
            });
        }

        if ($request->filled('city')) {
            $q->where('city', 'like', '%'.$request->string('city').'%');
        }
        if ($request->filled('country')) {
            $q->where('country', 'like', '%'.$request->string('country').'%');
        }
        if ($request->filled('capacity')) {
            $cap = (int) $request->input('capacity');
            $q->whereHas('rooms', function($qq) use ($cap) {
                $qq->where('capacity', '>=', $cap);
            });
        }
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $min = $request->input('min_price');
            $max = $request->input('max_price');
            $q->whereHas('rooms', function($qq) use ($min, $max) {
                if ($min !== null && $min !== '') {
                    $qq->where('price_per_night', '>=', (float) $min);
                }
                if ($max !== null && $max !== '') {
                    $qq->where('price_per_night', '<=', (float) $max);
                }
            });
        }

        // Date availability filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->input('start_date'))->startOfDay();
            $end   = Carbon::parse($request->input('end_date'))->startOfDay();
            if ($start <= $end) {
                $q->whereHas('rooms', function($qq) use ($start, $end) {
                    $qq->whereDoesntHave('bookings', function($b) use ($start, $end) {
                        $b->whereDate('start_date', '<', $end->toDateString())
                          ->whereDate('end_date', '>', $start->toDateString());
                    });
                });
            }
        }

        // Amenities filter (hotels that have ALL selected amenities)
        $amenities = Amenity::orderBy('name')->get();
        $rules = StayRule::orderBy('name')->get();
        $selectedAmenities = array_filter((array) $request->input('amenities', []));
        $selectedRules = array_filter((array) $request->input('rules', []));
        foreach ($selectedAmenities as $amenityId) {
            $q->whereHas('amenities', function($qa) use ($amenityId) {
                $qa->where('amenities.id', $amenityId);
            });
        }
        foreach ($selectedRules as $ruleId) {
            $q->whereHas('rules', function($qr) use ($ruleId) {
                $qr->where('rules.id', $ruleId);
            });
        }

        // Sorting
        $sort = $request->string('sort');
        switch ($sort) {
            case 'rooms':
                $q->orderByDesc('rooms_count');
                break;
            case 'price_asc':
                $q->orderBy('rooms_min_price_per_night');
                break;
            case 'price_desc':
                $q->orderByDesc('rooms_min_price_per_night');
                break;
            case 'stars':
                $q->orderByDesc('stars');
                break;
            case 'recent':
            default:
                $q->latest();
                break;
        }

        $hotels = $q->paginate(12)->withQueryString();
        return view('tourist.hotels.index', [
            'hotels' => $hotels,
            'filters' => $request->only(['q','sort','city','country','capacity','min_price','max_price','start_date','end_date','amenities','rules']),
            'amenities' => $amenities,
            'rules' => $rules,
        ]);
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['rooms.bookings', 'photos', 'amenities', 'rules', 'rooms.photos']);
        return view('tourist.hotels.show', compact('hotel'));
    }

    public function book(Request $request, Room $room)
    {
        $data = $request->validate([
            'start_date' => ['required','date'],
            'end_date'   => ['required','date','after_or_equal:start_date'],
        ]);

        // Restrict booking to tourists only
        if (Auth::check()) {
            $role = Auth::user()->role ?? 'tourist';
            if (in_array($role, ['hotel_manager','guide','restaurant_manager','admin'])) {
                return back()->withErrors([
                    'start_date' => "Seuls les touristes peuvent effectuer des réservations.",
                ])->withInput();
            }
        }

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->startOfDay();

        // Prevent hotel managers from booking their own published rooms
        $room->loadMissing('hotel');
        if (Auth::check() && $room->hotel && (Auth::user()->role === 'hotel_manager') && ($room->hotel->manager_id === Auth::id())) {
            return back()->withErrors([
                'start_date' => "Vous ne pouvez pas réserver vos propres chambres.",
            ])->withInput();
        }

        // Prevent overlapping reservations for the same room
        $conflict = HotelBooking::where('room_id', $room->id)
            ->where(function ($q) use ($start, $end) {
                // Overlap if new_start < existing_end AND new_end > existing_start
                $q->whereDate('start_date', '<', $end->toDateString())
                  ->whereDate('end_date', '>', $start->toDateString());
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'start_date' => 'La chambre est déjà réservée sur cette période.',
            ])->withInput();
        }

        $nights = max(1, $start->diffInDays($end));
        $total = $nights * (float) $room->price_per_night;

        $booking = HotelBooking::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'reference' => 'BK-'.now()->format('Ymd').'-'.strtoupper(str()->random(6)),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'status' => 'pending',
            'total_price' => $total,
        ]);

        return redirect()->route('tourist.bookings.show', $booking)->with('status', 'Réservation envoyée (en attente de confirmation).');
    }
}
