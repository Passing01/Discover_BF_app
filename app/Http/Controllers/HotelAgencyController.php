<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Amenity;
use App\Models\StayRule;
use App\Models\HotelPhoto;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Geocoding\NominatimGeocoder;

class HotelAgencyController extends Controller
{
    protected function ensureRole(): void
    {
        if ((Auth::user()?->role ?? null) !== 'hotel_manager' && !Auth::user()?->isAdmin()) {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureRole();
        $hotels = Hotel::where('manager_id', Auth::id())->with('rooms')->get();
        return view('agency.hotels.index', compact('hotels'));
    }

    public function show(Hotel $hotel)
    {
        $this->ensureRole();
        abort_unless($hotel->manager_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $hotel->load(['rooms.photos','photos','amenities','rules']);
        return view('agency.hotels.show', compact('hotel'));
    }

    public function create()
    {
        $this->ensureRole();
        $amenities = Amenity::orderBy('name')->get();
        $rules = StayRule::orderBy('name')->get();
        return view('agency.hotels.create', compact('amenities','rules'));
    }

    public function edit(Hotel $hotel)
    {
        $this->ensureRole();
        abort_unless($hotel->manager_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $amenities = Amenity::orderBy('name')->get();
        $rules = StayRule::orderBy('name')->get();
        $hotel->load('amenities','rules','photos');
        return view('agency.hotels.edit', compact('hotel','amenities','rules'));
    }

    public function store(Request $request)
    {
        $this->ensureRole();
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'address' => ['required','string','max:255'],
            'city' => ['required','string','max:120'],
            'country' => ['required','string','max:120'],
            'phone' => ['required','string','max:60'],
            'email' => ['required','email','max:120'],
            'description' => ['required','string'],
            'stars' => ['required','integer','min:1','max:5'],
            'photo' => ['nullable','image','max:4096'],
            // Allow auto-geocode when not provided
            'latitude' => ['nullable','numeric','between:-90,90'],
            'longitude' => ['nullable','numeric','between:-180,180'],
            'amenities' => ['nullable','array'],
            'amenities.*' => ['uuid'],
            'rules' => ['nullable','array'],
            'rules.*' => ['uuid'],
            'gallery' => ['nullable','array'],
            'gallery.*' => ['image','max:4096'],
        ]);
        $data['manager_id'] = Auth::id();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('hotels', 'public');
            $data['photo'] = 'storage/'.$path;
        }

        // Auto-geocode if latitude/longitude missing
        if (empty($data['latitude']) || empty($data['longitude'])) {
            $query = trim(($data['address'] ?? '').', '.($data['city'] ?? '').', '.($data['country'] ?? ''));
            $geo = app(NominatimGeocoder::class)->geocode($query);
            if ($geo) {
                [$lat, $lon] = $geo;
                $data['latitude'] = $lat;
                $data['longitude'] = $lon;
            }
        }

        // Final guard: if still missing, return validation error
        if (!isset($data['latitude'], $data['longitude'])) {
            return back()->withErrors([
                'address' => 'Impossible de géocoder cette adresse. Veuillez saisir la latitude et la longitude manuellement.',
            ])->withInput();
        }
        $hotel = Hotel::create($data);

        // Sync amenities & rules
        $hotel->amenities()->sync($request->input('amenities', []));
        $hotel->rules()->sync($request->input('rules', []));

        // Gallery photos
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $idx => $file) {
                if (!$file) continue;
                $path = $file->store('hotels', 'public');
                HotelPhoto::create([
                    'hotel_id' => $hotel->id,
                    'path' => 'storage/'.$path,
                    'position' => $idx,
                ]);
            }
        }
        return redirect()->route('agency.hotels.index')->with('status', 'Hôtel créé.');
    }

    public function update(Request $request, Hotel $hotel)
    {
        $this->ensureRole();
        abort_unless($hotel->manager_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'address' => ['required','string','max:255'],
            'city' => ['required','string','max:120'],
            'country' => ['required','string','max:120'],
            'phone' => ['required','string','max:60'],
            'email' => ['required','email','max:120'],
            'description' => ['required','string'],
            'stars' => ['required','integer','min:1','max:5'],
            'photo' => ['nullable','image','max:4096'],
            'latitude' => ['nullable','numeric','between:-90,90'],
            'longitude' => ['nullable','numeric','between:-180,180'],
            'amenities' => ['nullable','array'],
            'amenities.*' => ['uuid'],
            'rules' => ['nullable','array'],
            'rules.*' => ['uuid'],
            'gallery' => ['nullable','array'],
            'gallery.*' => ['image','max:4096'],
        ]);

        // Auto-geocode if missing
        if (empty($data['latitude']) || empty($data['longitude'])) {
            $query = trim(($data['address'] ?? '').', '.($data['city'] ?? '').', '.($data['country'] ?? ''));
            $geo = app(NominatimGeocoder::class)->geocode($query);
            if ($geo) {
                [$lat, $lon] = $geo;
                $data['latitude'] = $lat;
                $data['longitude'] = $lon;
            }
        }

        if (!isset($data['latitude'], $data['longitude'])) {
            return back()->withErrors([
                'address' => 'Impossible de géocoder cette adresse. Veuillez saisir la latitude et la longitude manuellement.',
            ])->withInput();
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('hotels', 'public');
            $data['photo'] = 'storage/'.$path;
        }

        $hotel->update($data);

        // Sync amenities & rules
        $hotel->amenities()->sync($request->input('amenities', []));
        $hotel->rules()->sync($request->input('rules', []));

        // Add gallery photos
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $idx => $file) {
                if (!$file) continue;
                $path = $file->store('hotels', 'public');
                HotelPhoto::create([
                    'hotel_id' => $hotel->id,
                    'path' => 'storage/'.$path,
                    'position' => $idx,
                ]);
            }
        }
        return redirect()->route('agency.hotels.index')->with('status', 'Hôtel mis à jour.');
    }

    public function createRoom(Hotel $hotel)
    {
        $this->ensureRole();
        abort_unless($hotel->manager_id === Auth::id() || Auth::user()->isAdmin(), 403);
        return view('agency.rooms.create', compact('hotel'));
    }

    public function storeRoom(Request $request, Hotel $hotel)
    {
        $this->ensureRole();
        abort_unless($hotel->manager_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'type' => ['required','string','max:60'],
            'price_per_night' => ['required','numeric','min:0'],
            'description' => ['nullable','string'],
            'capacity' => ['nullable','integer','min:1'],
            'photo' => ['nullable','image','max:4096'],
            'gallery' => ['nullable','array'],
            'gallery.*' => ['image','max:4096'],
        ]);
        $data['hotel_id'] = $hotel->id;
        $data['available'] = true;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('rooms', 'public');
            $data['photo'] = 'storage/'.$path;
        }
        $room = Room::create($data);

        // Room gallery photos
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $idx => $file) {
                if (!$file) continue;
                $path = $file->store('rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->id,
                    'path' => 'storage/'.$path,
                    'position' => $idx,
                ]);
            }
        }
        return redirect()->route('agency.hotels.index')->with('status', 'Chambre ajoutée.');
    }
}
