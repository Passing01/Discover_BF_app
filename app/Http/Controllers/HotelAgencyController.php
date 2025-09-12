<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Amenity;
use App\Models\StayRule;
use App\Models\HotelPhoto;
use App\Models\RoomPhoto;
use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Geocoding\NominatimGeocoder;
use Carbon\Carbon;

class HotelAgencyController extends Controller
{
    protected $user;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hotel_manager');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    protected function ensureRole(): void
    {
        if ($this->user->role !== 'hotel_manager' && !$this->user->isAdmin()) {
            abort(403);
        }
    }

    /**
     * Afficher la liste des hôtels du gestionnaire
     */
    public function index()
    {
        $hotels = Hotel::where('manager_id', $this->user->id)
            ->withCount(['rooms', 'bookings as active_bookings' => function($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            }])
            ->with(['rooms' => function($query) {
                $query->withCount(['bookings' => function($q) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                }]);
            }])
            ->orderBy('name')
            ->paginate(10);
            
        return view('hotel-manager.hotels.index', compact('hotels'));
    }

    /**
     * Afficher les détails d'un hôtel
     */
    public function show(Hotel $hotel)
    {
        $this->authorize('view', $hotel);
        
        $hotel->load([
            'rooms' => function($query) {
                $query->withCount(['bookings' => function($q) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                }]);
            },
            'photos',
            'amenities',
            'rules',
            'bookings' => function($query) {
                $query->with(['room'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('check_in')
                    ->take(5);
            }
        ]);
        
        // Statistiques pour l'hôtel
        $stats = [
            'total_rooms' => $hotel->rooms->count(),
            'available_rooms' => $hotel->rooms->where('is_available', true)->count(),
            'active_bookings' => $hotel->bookings->count(),
            'monthly_revenue' => $hotel->bookings()
                ->where('status', 'completed')
                ->whereMonth('check_in', now()->month)
                ->sum('total_price'),
        ];
        
        return view('hotel-manager.hotels.show', compact('hotel', 'stats'));
    }

    /**
     * Afficher le formulaire de création d'un hôtel
     */
    public function create()
    {
        $this->authorize('create', Hotel::class);
        
        $amenities = Amenity::orderBy('name')->get();
        $rules = StayRule::orderBy('name')->get();
        
        return view('hotel-manager.hotels.create', compact('amenities', 'rules'));
    }

    /**
     * Afficher le formulaire de modification d'un hôtel
     */
    public function edit(Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        
        $amenities = Amenity::orderBy('name')->get();
        $rules = StayRule::orderBy('name')->get();
        $hotel->load('amenities', 'rules', 'photos');
        
        return view('hotel-manager.hotels.edit', compact('hotel', 'amenities', 'rules'));
    }

    /**
     * Enregistrer un nouvel hôtel
     */
    public function store(Request $request)
    {
        $this->authorize('create', Hotel::class);
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:120'],
            'description' => ['required', 'string'],
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i', 'after:check_in_time'],
            'cancellation_policy' => ['required', 'string', 'in:flexible,moderate,strict'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['uuid', 'exists:amenities,id'],
            'rules' => ['nullable', 'array'],
            'rules.*' => ['uuid', 'exists:stay_rules,id'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:4096'],
        ]);
        
        $data['manager_id'] = $this->user->id;
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        
        // Gestion de la photo principale
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('hotels', 'public');
            $data['photo'] = 'storage/'.$path;
        }

        // Géocodage automatique si les coordonnées ne sont pas fournies
        if (empty($data['latitude']) || empty($data['longitude'])) {
            $query = trim(($data['address'] ?? '').', '.($data['city'] ?? '').', '.($data['country'] ?? ''));
            $geo = app(NominatimGeocoder::class)->geocode($query);
            if ($geo) {
                [$lat, $lon] = $geo;
                $data['latitude'] = $lat;
                $data['longitude'] = $lon;
            }
        }

        // Vérification finale des coordonnées
        if (empty($data['latitude']) || empty($data['longitude'])) {
            return back()
                ->withErrors(['address' => 'Impossible de géocoder cette adresse. Veuillez saisir la latitude et la longitude manuellement.'])
                ->withInput();
        }
        
        // Création de l'hôtel dans une transaction pour assurer l'intégrité des données
        $hotel = DB::transaction(function () use ($data, $request) {
            $hotel = Hotel::create($data);
            
            // Synchronisation des équipements et des règles
            if (isset($data['amenities'])) {
                $hotel->amenities()->sync($data['amenities']);
            }
            
            if (isset($data['rules'])) {
                $hotel->rules()->sync($data['rules']);
            }
            
            // Gestion de la galerie photo
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $idx => $file) {
                    if (!$file) continue;
                    $path = $file->store('hotels', 'public');
                    $hotel->photos()->create([
                        'path' => 'storage/'.$path,
                        'position' => $idx,
                    ]);
                }
            }
            
            return $hotel;
        });
        
        return redirect()
            ->route('hotel-manager.hotels.show', $hotel)
            ->with('success', 'Hôtel créé avec succès.');
    }

    /**
     * Mettre à jour un hôtel existant
     */
    public function update(Request $request, Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:120'],
            'description' => ['required', 'string'],
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i', 'after:check_in_time'],
            'cancellation_policy' => ['required', 'string', 'in:flexible,moderate,strict'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['uuid', 'exists:amenities,id'],
            'rules' => ['nullable', 'array'],
            'rules.*' => ['uuid', 'exists:stay_rules,id'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:4096'],
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['uuid', 'exists:hotel_photos,id'],
        ]);
        
        // Mise à jour du slug si le nom a changé
        if ($hotel->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }
        
        // Gestion de la photo principale
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($hotel->photo) {
                Storage::delete(str_replace('storage/', 'public/', $hotel->photo));
            }
            
            $path = $request->file('photo')->store('hotels', 'public');
            $data['photo'] = 'storage/'.$path;
        }

        // Géocodage automatique si les coordonnées ne sont pas fournies ou si l'adresse a changé
        $addressChanged = $hotel->address !== $data['address'] || 
                         $hotel->city !== $data['city'] || 
                         $hotel->country !== $data['country'];
                         
        if ((empty($data['latitude']) || empty($data['longitude'])) && $addressChanged) {
            $query = trim(($data['address'] ?? '').', '.($data['city'] ?? '').', '.($data['country'] ?? ''));
            $geo = app(NominatimGeocoder::class)->geocode($query);
            if ($geo) {
                [$lat, $lon] = $geo;
                $data['latitude'] = $lat;
                $data['longitude'] = $lon;
            }
        }

        // Vérification finale des coordonnées
        if (empty($data['latitude']) || empty($data['longitude'])) {
            return back()
                ->withErrors(['address' => 'Impossible de géocoder cette adresse. Veuillez saisir la latitude et la longitude manuellement.'])
                ->withInput();
        }
        
        // Mise à jour de l'hôtel dans une transaction
        DB::transaction(function () use ($hotel, $data, $request) {
            // Mise à jour des données de base
            $hotel->update($data);
            
            // Synchronisation des équipements et des règles
            $hotel->amenities()->sync($request->input('amenities', []));
            $hotel->rules()->sync($request->input('rules', []));
            
            // Suppression des photos sélectionnées
            if ($request->has('delete_photos')) {
                $photosToDelete = $hotel->photos()->whereIn('id', $request->delete_photos)->get();
                
                foreach ($photosToDelete as $photo) {
                    Storage::delete(str_replace('storage/', 'public/', $photo->path));
                    $photo->delete();
                }
            }
            
            // Ajout des nouvelles photos à la galerie
            if ($request->hasFile('gallery')) {
                $position = $hotel->photos()->max('position') + 1;
                
                foreach ($request->file('gallery') as $file) {
                    if (!$file) continue;
                    $path = $file->store('hotels', 'public');
                    $hotel->photos()->create([
                        'path' => 'storage/'.$path,
                        'position' => $position++,
                    ]);
                }
            }
        });
        
        return redirect()
            ->route('hotel-manager.hotels.show', $hotel)
            ->with('success', 'Les informations de l\'hôtel ont été mises à jour avec succès.');
    }

    /**
     * Afficher le formulaire de création d'une chambre
     */
    public function createRoom(Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        
        $roomTypes = [
            'single' => 'Chambre Simple',
            'double' => 'Chambre Double',
            'twin' => 'Chambre Lits Jumeaux',
            'triple' => 'Chambre Triple',
            'quad' => 'Chambre Quadruple',
            'suite' => 'Suite',
            'family' => 'Chambre Familiale',
            'dormitory' => 'Dortoir',
        ];
        
        $bedTypes = [
            'single' => 'Lit simple (90x190)',
            'double' => 'Grand lit (140x190)',
            'queen' => 'Queen size (160x200)',
            'king' => 'King size (180x200)',
            'bunk' => 'Lits superposés',
            'sofa' => 'Canapé-lit',
        ];
        
        return view('hotel-manager.rooms.create', compact('hotel', 'roomTypes', 'bedTypes'));
    }

    /**
     * Enregistrer une nouvelle chambre
     */
    public function storeRoom(Request $request, Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', 'in:single,double,twin,triple,quad,suite,family,dormitory'],
            'bed_type' => ['required', 'string', 'in:single,double,queen,king,bunk,sofa'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'discount_weekly' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_monthly' => ['nullable', 'integer', 'min:0', 'max:100'],
            'size' => ['nullable', 'numeric', 'min:0'],
            'max_adults' => ['required', 'integer', 'min:1'],
            'max_children' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['uuid', 'exists:amenities,id'],
            'beds' => ['required', 'array', 'min:1'],
            'beds.*.type' => ['required', 'string', 'in:single,double,queen,king,bunk,sofa'],
            'beds.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:4096'],
        ]);
        
        $data['hotel_id'] = $hotel->id;
        $data['is_available'] = $request->has('is_available');
        $data['has_breakfast'] = $request->has('has_breakfast');
        $data['has_refundable'] = $request->has('has_refundable');
        $data['has_free_cancellation'] = $request->has('has_free_cancellation');
        $data['beds'] = json_encode($data['beds']);
        
        // Création de la chambre dans une transaction
        $room = DB::transaction(function () use ($data, $request, $hotel) {
            // Gestion de la photo principale
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('rooms', 'public');
                $data['photo'] = 'storage/'.$path;
            }
            
            $room = Room::create($data);
            
            // Synchronisation des équipements
            if (isset($data['amenities'])) {
                $room->amenities()->sync($data['amenities']);
            }
            
            // Gestion de la galerie photo
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $idx => $file) {
                    if (!$file) continue;
                    $path = $file->store('rooms', 'public');
                    $room->photos()->create([
                        'path' => 'storage/'.$path,
                        'position' => $idx,
                    ]);
                }
            }
            
            return $room;
        });
        
        return redirect()
            ->route('hotel-manager.rooms.show', $room)
            ->with('success', 'La chambre a été ajoutée avec succès.');
    }
    
    /**
     * Afficher les détails d'une chambre
     */
    public function showRoom(Room $room)
    {
        $this->authorize('view', $room->hotel);
        
        $room->load(['hotel', 'photos', 'amenities', 'bookings' => function($query) {
            $query->whereIn('status', ['pending', 'confirmed'])
                  ->orderBy('check_in');
        }]);
        
        // Statistiques pour la chambre
        $stats = [
            'total_bookings' => $room->bookings()->count(),
            'upcoming_bookings' => $room->bookings()
                ->where('check_in', '>=', now())
                ->count(),
            'revenue_30days' => $room->bookings()
                ->where('status', 'completed')
                ->where('check_out', '>=', now()->subDays(30))
                ->sum('total_price'),
            'occupancy_rate' => $this->calculateOccupancyRate($room),
        ];
        
        return view('hotel-manager.rooms.show', compact('room', 'stats'));
    }
    
    /**
     * Afficher le formulaire de modification d'une chambre
     */
    public function editRoom(Room $room)
    {
        $this->authorize('update', $room->hotel);
        
        $roomTypes = [
            'single' => 'Chambre Simple',
            'double' => 'Chambre Double',
            'twin' => 'Chambre Lits Jumeaux',
            'triple' => 'Chambre Triple',
            'quad' => 'Chambre Quadruple',
            'suite' => 'Suite',
            'family' => 'Chambre Familiale',
            'dormitory' => 'Dortoir',
        ];
        
        $bedTypes = [
            'single' => 'Lit simple (90x190)',
            'double' => 'Grand lit (140x190)',
            'queen' => 'Queen size (160x200)',
            'king' => 'King size (180x200)',
            'bunk' => 'Lits superposés',
            'sofa' => 'Canapé-lit',
        ];
        
        $room->load('photos', 'amenities');
        
        return view('hotel-manager.rooms.edit', compact('room', 'roomTypes', 'bedTypes'));
    }
    
    /**
     * Mettre à jour une chambre existante
     */
    public function updateRoom(Request $request, Room $room)
    {
        $this->authorize('update', $room->hotel);
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', 'in:single,double,twin,triple,quad,suite,family,dormitory'],
            'bed_type' => ['required', 'string', 'in:single,double,queen,king,bunk,sofa'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'discount_weekly' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_monthly' => ['nullable', 'integer', 'min:0', 'max:100'],
            'size' => ['nullable', 'numeric', 'min:0'],
            'max_adults' => ['required', 'integer', 'min:1'],
            'max_children' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['uuid', 'exists:amenities,id'],
            'beds' => ['required', 'array', 'min:1'],
            'beds.*.type' => ['required', 'string', 'in:single,double,queen,king,bunk,sofa'],
            'beds.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:4096'],
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['uuid', 'exists:room_photos,id'],
        ]);
        
        $data['is_available'] = $request->has('is_available');
        $data['has_breakfast'] = $request->has('has_breakfast');
        $data['has_refundable'] = $request->has('has_refundable');
        $data['has_free_cancellation'] = $request->has('has_free_cancellation');
        $data['beds'] = json_encode($data['beds']);
        
        // Mise à jour de la chambre dans une transaction
        DB::transaction(function () use ($room, $data, $request) {
            // Gestion de la photo principale
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($room->photo) {
                    Storage::delete(str_replace('storage/', 'public/', $room->photo));
                }
                
                $path = $request->file('photo')->store('rooms', 'public');
                $data['photo'] = 'storage/'.$path;
            }
            
            // Mise à jour des données de base
            $room->update($data);
            
            // Synchronisation des équipements
            $room->amenities()->sync($request->input('amenities', []));
            
            // Suppression des photos sélectionnées
            if ($request->has('delete_photos')) {
                $photosToDelete = $room->photos()->whereIn('id', $request->delete_photos)->get();
                
                foreach ($photosToDelete as $photo) {
                    Storage::delete(str_replace('storage/', 'public/', $photo->path));
                    $photo->delete();
                }
            }
            
            // Ajout des nouvelles photos à la galerie
            if ($request->hasFile('gallery')) {
                $position = $room->photos()->max('position') + 1;
                
                foreach ($request->file('gallery') as $file) {
                    if (!$file) continue;
                    $path = $file->store('rooms', 'public');
                    $room->photos()->create([
                        'path' => 'storage/'.$path,
                        'position' => $position++,
                    ]);
                }
            }
        });
        
        return redirect()
            ->route('hotel-manager.rooms.show', $room)
            ->with('success', 'Les informations de la chambre ont été mises à jour avec succès.');
    }
    
    /**
     * Supprimer une chambre
     */
    public function destroyRoom(Room $room)
    {
        $this->authorize('delete', $room->hotel);
        
        // Vérifier qu'il n'y a pas de réservations futures
        $hasFutureBookings = $room->bookings()
            ->where('check_out', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
            
        if ($hasFutureBookings) {
            return back()
                ->with('error', 'Impossible de supprimer cette chambre car elle a des réservations futures.');
        }
        
        // Suppression dans une transaction
        DB::transaction(function () use ($room) {
            // Supprimer les photos de la galerie
            foreach ($room->photos as $photo) {
                Storage::delete(str_replace('storage/', 'public/', $photo->path));
                $photo->delete();
            }
            
            // Supprimer la photo principale
            if ($room->photo) {
                Storage::delete(str_replace('storage/', 'public/', $room->photo));
            }
            
            // Détacher les relations
            $room->amenities()->detach();
            
            // Supprimer la chambre
            $room->delete();
        });
        
        return redirect()
            ->route('hotel-manager.hotels.show', $room->hotel)
            ->with('success', 'La chambre a été supprimée avec succès.');
    }
    
    /**
     * Gérer la disponibilité des chambres
     */
    public function availability(Request $request, Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        
        $request->validate([
            'room_id' => ['nullable', 'exists:rooms,id,hotel_id,'.$hotel->id],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date', 'before_or_equal:' . now()->addYear()->format('Y-m-d')],
            'status' => ['required', 'in:available,unavailable'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'min_nights' => ['nullable', 'integer', 'min:1'],
            'max_guests' => ['nullable', 'integer', 'min:1'],
        ]);
        
        $query = $hotel->rooms();
        
        if ($request->room_id) {
            $query->where('id', $request->room_id);
        }
        
        $rooms = $query->get();
        
        if ($rooms->isEmpty()) {
            return back()->with('error', 'Aucune chambre trouvée.');
        }
        
        // Mise à jour de la disponibilité pour chaque chambre
        foreach ($rooms as $room) {
            $room->availabilities()->updateOrCreate(
                [
                    'date' => $request->start_date,
                    'end_date' => $request->end_date,
                ],
                [
                    'status' => $request->status === 'available' ? 'available' : 'unavailable',
                    'price' => $request->price,
                    'min_nights' => $request->min_nights,
                    'max_guests' => $request->max_guests,
                ]
            );
        }
        
        $message = $rooms->count() > 1 
            ? 'La disponibilité a été mise à jour pour ' . $rooms->count() . ' chambres.'
            : 'La disponibilité a été mise à jour pour la chambre sélectionnée.';
        
        return back()->with('success', $message);
    }
    
    /**
     * Calculer le taux d'occupation d'une chambre
     */
    protected function calculateOccupancyRate(Room $room, $months = 6)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->addMonths($months - 1)->endOfMonth();
        
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $bookedDays = 0;
        
        // Récupérer toutes les réservations qui se chevauchent avec la période
        $bookings = $room->bookings()
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in', [$startDate, $endDate])
                      ->orWhereBetween('check_out', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('check_in', '<', $startDate)
                            ->where('check_out', '>', $endDate);
                      });
            })
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();
        
        // Calculer le nombre de jours réservés
        foreach ($bookings as $booking) {
            $checkIn = Carbon::parse($booking->check_in)->max($startDate);
            $checkOut = Carbon::parse($booking->check_out)->min($endDate);
            $bookedDays += $checkIn->diffInDays($checkOut);
        }
        
        return $totalDays > 0 ? round(($bookedDays / $totalDays) * 100, 2) : 0;
    }
}
