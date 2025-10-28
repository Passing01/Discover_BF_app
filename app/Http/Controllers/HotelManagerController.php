<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\HotelBooking;
use App\Models\Amenity;
use App\Models\StayRule;
use App\Models\RoomType;
use App\Models\Photo;
use App\Services\Geocoding\NominatimGeocoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HotelManagerController extends Controller
{
    protected $geocoder;

    protected $user;

    public function index()
    {
        $hotels = Auth::user()->managedHotels()
            ->withCount(['rooms', 'activeBookings', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('hotel-manager.hotels.index', compact('hotels'));
    }
    
    public function __construct(NominatimGeocoder $geocoder)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
        $this->geocoder = $geocoder;
    }

    /**
     * Affiche le tableau de bord du gestionnaire d'hôtel
     */
    public function dashboard()
    {
        $user = $this->user;
        
        $hotels = $user->managedHotels()
            ->withCount(['rooms', 'activeBookings', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(5);

        // Statistiques globales
        $stats = [
            'total_hotels' => $user->managedHotels()->count(),
            'total_rooms' => $user->managedHotels()->withCount('rooms')->get()->sum('rooms_count'),
            'active_bookings' => $user->managedHotels()->withCount(['bookings' => function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in']);
            }])->get()->sum('bookings_count'),
            'monthly_revenue' => $user->managedHotels()->withSum(['bookings' => function($query) {
                $query->whereMonth('hotel_bookings.created_at', now()->month)
                      ->whereIn('status', ['confirmed', 'checked_in', 'completed']);
            }], 'total_price')->get()->sum('bookings_sum_total_price') ?? 0,
        ];

        // Dernières réservations
        $recentBookings = HotelBooking::whereHas('room', function($query) use ($user) {
                $query->whereIn('hotel_id', $user->managedHotels()->pluck('id'));
            })
            ->with(['room.hotel', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Récupérer le premier hôtel comme hôtel par défaut
        $defaultHotel = $user->managedHotels()->first();

        return view('hotel-manager.dashboard', compact('hotels', 'stats', 'recentBookings', 'defaultHotel'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel hôtel
     */
    public function create()
    {
        $amenities = Amenity::all();
        $rules = StayRule::all();
        return view('hotel-manager.hotels.create', compact('amenities', 'rules'));
    }

    /**
     * Enregistre un nouvel hôtel
     */
    public function store(Request $request)
    {
        // Log de débogage
        \Log::info('Début de la création d\'hôtel', $request->except(['main_photo', 'photos']));
        
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i|after:check_in_time',
            'cancellation_policy' => 'required|in:flexible,moderate,strict,non_refundable',
            'min_stay' => 'required|integer|min:1|max:30',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'rules' => 'required|array|min:1',
            'rules.*' => 'string|max:255',
            'main_photo' => 'required|image|max:5120|mimes:jpeg,png,jpg,gif',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120|mimes:jpeg,png,jpg,gif',
        ], [
            'rules.required' => 'Veuillez ajouter au moins une règle pour votre hôtel.',
            'main_photo.required' => 'Une photo principale est requise.',
            'check_out_time.after' => 'L\'heure de départ doit être postérieure à l\'heure d\'arrivée.',
        ]);
        
        \Log::info('Validation réussie', $validated);

        DB::beginTransaction();
        
        try {
            // Géocodage de l'adresse (optionnel)
            $coordinates = null;
            $fullAddress = "{$validated['address']}, {$validated['postal_code']} {$validated['city']}, {$validated['country']}";
            
            try {
                $coordinates = $this->geocoder->geocode($fullAddress);
                if ($coordinates) {
                    \Log::info('Géocodage réussi', ['coordinates' => $coordinates]);
                } else {
                    \Log::warning('Aucun résultat de géocodage pour l\'adresse', ['address' => $fullAddress]);
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur lors du géocodage', [
                    'error' => $e->getMessage(),
                    'address' => $fullAddress
                ]);
                // On continue même si le géocodage échoue
            }

            // Préparation des données de l'hôtel
            $hotelData = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'website' => $validated['website'] ?? null,
                'check_in_time' => $validated['check_in_time'],
                'check_out_time' => $validated['check_out_time'],
                'cancellation_policy' => $validated['cancellation_policy'],
                'min_stay' => $validated['min_stay'] ?? 1,
                'stars' => $validated['stars'] ?? 3, // Ajout du champ stars avec valeur par défaut 3
                'latitude' => 0.0, // Valeur par défaut pour latitude
                'longitude' => 0.0, // Valeur par défaut pour longitude
                'status' => 'pending',
                'is_active' => true,
                'is_approved' => false,
                'is_featured' => false,
                'manager_id' => Auth::id(),
                'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            ];

            // Ajout des coordonnées si disponibles
            if ($coordinates && is_array($coordinates) && count($coordinates) >= 2) {
                $hotelData['latitude'] = $coordinates[0];
                $hotelData['longitude'] = $coordinates[1];
            }

            \Log::info('Création de l\'hôtel', $hotelData);
            $hotel = Hotel::create($hotelData);
            \Log::info('Hôtel créé avec succès', ['hotel_id' => $hotel->id]);

            // Création du répertoire de stockage s'il n'existe pas
            $storagePath = 'hotels/' . $hotel->id;
            if (!Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->makeDirectory($storagePath, 0755, true);
            }

            // Gestion de la photo principale
            if ($request->hasFile('main_photo')) {
                $file = $request->file('main_photo');
                \Log::info('Traitement de la photo principale', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ]);
                
                try {
                    $filename = 'main_' . time() . '.' . $file->getClientOriginalExtension();
                    \Log::info('Tentative de stockage de la photo principale', [
                        'filename' => $filename,
                        'storage_path' => $storagePath
                    ]);
                    
                    // Stockage du fichier
                    $path = $file->storeAs($storagePath, $filename, 'public');
                    \Log::info('Photo stockée avec succès', ['path' => $path]);
                    
                    // Vérification que le fichier existe bien
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception("Le fichier n'a pas été correctement enregistré sur le disque");
                    }
                    
                    // Enregistrement dans la base de données
                    $photo = $hotel->photos()->create([
                        'path' => $path,
                        'is_main' => true,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                    
                    \Log::info('Photo principale enregistrée dans la base de données', [
                        'photo_id' => $photo->id,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize()
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'enregistrement de la photo principale', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'hotel_id' => $hotel->id,
                        'file' => [
                            'name' => $file ? $file->getClientOriginalName() : null,
                            'size' => $file ? $file->getSize() : null,
                            'mime' => $file ? $file->getMimeType() : null
                        ]
                    ]);
                    throw $e;
                }
            }

            // Gestion des photos supplémentaires
            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');
                \Log::info('Traitement des photos supplémentaires', ['count' => count($photos)]);
                
                foreach ($photos as $index => $photo) {
                    \Log::info("Traitement de la photo supplémentaire #$index", [
                        'original_name' => $photo->getClientOriginalName(),
                        'size' => $photo->getSize(),
                        'mime' => $photo->getMimeType(),
                        'extension' => $photo->getClientOriginalExtension()
                    ]);
                    
                    try {
                        $filename = 'photo_' . time() . '_' . $index . '.' . $photo->getClientOriginalExtension();
                        \Log::info("Tentative de stockage de la photo supplémentaire #$index", [
                            'filename' => $filename,
                            'storage_path' => $storagePath
                        ]);
                        
                        // Stockage du fichier
                        $path = $photo->storeAs($storagePath, $filename, 'public');
                        \Log::info("Photo supplémentaire #$index stockée avec succès", ['path' => $path]);
                        
                        // Vérification que le fichier existe bien
                        if (!Storage::disk('public')->exists($path)) {
                            throw new \Exception("Le fichier n'a pas été correctement enregistré sur le disque");
                        }
                        
                        // Enregistrement dans la base de données
                        $photoModel = $hotel->photos()->create([
                            'path' => $path,
                            'is_main' => false,
                            'original_name' => $photo->getClientOriginalName(),
                            'mime_type' => $photo->getMimeType(),
                            'size' => $photo->getSize()
                        ]);
                        
                        \Log::info("Photo supplémentaire #$index enregistrée avec succès", [
                            'photo_id' => $photoModel->id,
                            'path' => $path,
                            'original_name' => $photo->getClientOriginalName(),
                            'size' => $photo->getSize()
                        ]);
                        
                    } catch (\Exception $e) {
                        \Log::error("Erreur lors de l'enregistrement de la photo supplémentaire #$index", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'hotel_id' => $hotel->id,
                            'index' => $index,
                            'file' => [
                                'name' => $photo ? $photo->getClientOriginalName() : null,
                                'size' => $photo ? $photo->getSize() : null,
                                'mime' => $photo ? $photo->getMimeType() : null
                            ]
                        ]);
                        // On continue avec les autres photos
                    }
                }
            }

            // Synchronisation des équipements
            if (!empty($validated['amenities'])) {
                $hotel->amenities()->sync($validated['amenities']);
                \Log::info('Équipements synchronisés', ['amenities' => $validated['amenities']]);
            }
            
            // Gestion des règles
            $ruleIds = [];
            if (!empty($validated['rules'])) {
                foreach ($validated['rules'] as $ruleName) {
                    // Vérifier si la règle existe déjà
                    $rule = StayRule::firstOrCreate(
                        ['name' => $ruleName],
                        ['name' => $ruleName]
                    );
                    $ruleIds[] = $rule->id;
                }
            }
            
            // Synchronisation des règles
            $hotel->rules()->sync($ruleIds);

            DB::commit();
            \Log::info('Hôtel créé avec succès, redirection vers la liste des hôtels', ['hotel_id' => $hotel->id]);

            return redirect()->route('hotel-manager.hotels.index')
                ->with('success', 'Hôtel créé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'hôtel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['main_photo', 'photos'])
            ]);
            
            DB::rollBack();
            \Log::error('Rollback effectué après erreur');
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'hôtel. Détails : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un hôtel
     */
    public function show(Hotel $hotel)
    {
        // $this->authorize('hotel-manager', $hotel);
        
        // Charger les relations nécessaires
        $hotel->load([
            'rooms', 
            'photos', 
            'amenities', 
            'rules',
            'reviews' => function($query) {
                $query->with('user')
                    ->orderBy('reviews.created_at', 'desc')
                    ->take(5);
            },
            'activeBookings' => function($query) {
                $query->with(['room', 'user'])
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->orderBy('hotel_bookings.created_at', 'desc')
                    ->take(5);
            }
        ]);
        
        // Calculer le taux d'occupation
        $totalRooms = $hotel->rooms->count();
        $bookedRooms = $hotel->activeBookings->count();
        $occupancyRate = $totalRooms > 0 ? round(($bookedRooms / $totalRooms) * 100, 1) : 0;
        
        // Récupérer les réservations récentes
        $recentBookings = $hotel->activeBookings->sortByDesc('created_at')->take(5);

        return view('hotel-manager.hotels.show', compact('hotel', 'occupancyRate', 'recentBookings'));
    }

    /**
     * Affiche le formulaire de modification d'un hôtel
     */
    public function edit(Hotel $hotel)
    {
        // $this->authorize('hotel-manager', $hotel);

        $hotel->load(['amenities', 'rules', 'photos']);
        $amenities = Amenity::all();
        $rules = StayRule::all();

        return view('hotel-manager.hotels.edit', compact('hotel', 'amenities', 'rules'));
    }

    /**
     * Met à jour un hôtel existant
     */
    public function update(Request $request, Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'website' => 'nullable|url',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i|after:check_in_time',
            'cancellation_policy' => 'required|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'rules' => 'array',
            'rules.*' => 'exists:rules,name',
            'main_photo' => 'nullable|image|max:5120',
            'photos.*' => 'image|max:5120',
            'delete_photos' => 'array',
            'delete_photos.*' => 'exists:photos,id',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier si l'adresse a changé pour le géocodage
            $addressChanged = $hotel->address !== $validated['address'] ||
                            $hotel->city !== $validated['city'] ||
                            $hotel->country !== $validated['country'] ||
                            $hotel->postal_code !== $validated['postal_code'];

            if ($addressChanged) {
                $fullAddress = "{$validated['address']}, {$validated['postal_code']} {$validated['city']}, {$validated['country']}";
                $coordinates = $this->geocoder->getCoordinates($fullAddress);
                
                $hotel->update([
                    'latitude' => $coordinates['lat'] ?? $hotel->latitude,
                    'longitude' => $coordinates['lon'] ?? $hotel->longitude,
                ]);
            }

            // Mise à jour des informations de base
            $hotel->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'website' => $validated['website'] ?? null,
                'check_in_time' => $validated['check_in_time'],
                'check_out_time' => $validated['check_out_time'],
                'cancellation_policy' => $validated['cancellation_policy'],
            ]);

            // Gestion de la photo principale
            if ($request->hasFile('main_photo')) {
                // Supprimer l'ancienne photo principale
                $oldMainPhoto = $hotel->photos()->where('is_main', true)->first();
                if ($oldMainPhoto) {
                    Storage::disk('public')->delete($oldMainPhoto->path);
                    $oldMainPhoto->delete();
                }

                // Ajouter la nouvelle photo principale
                $path = $request->file('main_photo')->store('hotels/' . $hotel->id, 'public');
                $hotel->photos()->create([
                    'path' => $path,
                    'is_main' => true,
                ]);
            }

            // Ajout de nouvelles photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('hotels/' . $hotel->id, 'public');
                    $hotel->photos()->create([
                        'path' => $path,
                        'is_main' => false,
                    ]);
                }
            }

            // Suppression des photos sélectionnées
            if (!empty($validated['delete_photos'])) {
                $photosToDelete = $hotel->photos()
                    ->whereIn('id', $validated['delete_photos'])
                    ->where('is_main', false)
                    ->get();

                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }

            // Synchronisation des équipements
            $hotel->amenities()->sync($validated['amenities'] ?? []);
            
            // Gestion des règles
            $ruleIds = [];
            if (!empty($validated['rules'])) {
                foreach ($validated['rules'] as $ruleName) {
                    // Vérifier si la règle existe déjà
                    $rule = StayRule::firstOrCreate(
                        ['name' => $ruleName],
                        ['name' => $ruleName]
                    );
                    $ruleIds[] = $rule->id;
                }
            }
            
            // Synchronisation des règles
            $hotel->rules()->sync($ruleIds);

            DB::commit();

            return redirect()->route('hotel-manager.hotels.show', $hotel->id)
                ->with('success', 'Hôtel mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'hôtel.');
        }
    }

    /**
     * Supprime un hôtel
     */
    public function destroy(Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);

        // Vérifier s'il y a des réservations futures
        $hasFutureBookings = $hotel->bookings()
            ->where('check_out', '>=', now())
            ->exists();

        if ($hasFutureBookings) {
            return back()->with('error', 'Impossible de supprimer cet hôtel car il a des réservations futures.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les photos du stockage
            foreach ($hotel->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            // Supprimer les chambres et leurs photos
            foreach ($hotel->rooms as $room) {
                foreach ($room->photos as $photo) {
                    Storage::disk('public')->delete($photo->path);
                }
                $room->photos()->delete();
                $room->delete();
            }

            // Supprimer l'hôtel et ses relations
            $hotel->photos()->delete();
            $hotel->amenities()->detach();
            $hotel->rules()->detach();
            $hotel->reviews()->delete();
            $hotel->delete();

            // Supprimer le dossier de l'hôtel
            Storage::disk('public')->deleteDirectory('hotels/' . $hotel->id);

            DB::commit();

            return redirect()->route('hotel-manager.hotels.index')
                ->with('success', 'Hôtel supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'hôtel.');
        }
    }

    /**
     * Affiche la liste de toutes les chambres de tous les hôtels du gestionnaire
     */
    public function listRooms()
    {
        $hotels = $this->user->managedHotels()
            ->with(['rooms' => function($query) {
                $query->withCount('bookings')
                    ->with('photos');
            }])
            ->get();

        return view('hotel-manager.rooms.list', compact('hotels'));
    }

    /**
     * Affiche la liste des chambres d'un hôtel
     */
    public function rooms(Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);

        $rooms = $hotel->rooms()
            ->withCount(['bookings' => function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in']);
            }])
            ->with('photos')
            ->latest()
            ->paginate(10);

        return view('hotel-manager.rooms.index', compact('hotel', 'rooms'));
    }

    /**
     * Affiche le formulaire de création d'une chambre
     */
    public function createRoom(Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);

        // Utiliser les types de chambre depuis la table room_types
        $roomTypes = RoomType::orderBy('name')->get();
        $amenities = Amenity::where('name', 'room')->get();

        return view('hotel-manager.rooms.create', compact('hotel', 'roomTypes', 'amenities'));
    }

    /**
     * Enregistre une nouvelle chambre
     */
    public function storeRoom(Request $request, Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);
        
        // Log des données reçues
        \Log::info('Données reçues pour la création de chambre :', $request->all());
        \Log::info('Fichiers reçus :', ['photos_count' => $request->hasFile('photos') ? count($request->file('photos')) : 0]);
        
        $rules = [
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:10|unique:rooms,room_number,NULL,id,hotel_id,' . $hotel->id,
            'floor' => 'required|integer|min:-10|max:200',
            'size' => 'nullable|integer|min:1',
            'bed_type' => 'required|in:single,double,queen,king,twin,multiple',
            'bed_count' => 'required|integer|min:1|max:10',
            'max_occupancy' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:10',
            'min_stay' => 'nullable|integer|min:1',
            'max_adults' => 'nullable|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'view' => 'nullable|string|max:100',
            'is_smoking_allowed' => 'sometimes|boolean',
            'has_balcony' => 'sometimes|boolean',
            'has_terrace' => 'sometimes|boolean',
            'has_sea_view' => 'sometimes|boolean',
            'has_lake_view' => 'sometimes|boolean',
            'has_mountain_view' => 'sometimes|boolean',
            'has_bathtub' => 'sometimes|boolean',
            'has_shower' => 'sometimes|boolean',
            'has_air_conditioning' => 'sometimes|boolean',
            'has_heating' => 'sometimes|boolean',
            'has_tv' => 'sometimes|boolean',
            'has_phone' => 'sometimes|boolean',
            'has_safe' => 'sometimes|boolean',
            'has_mini_bar' => 'sometimes|boolean',
            'has_electric_kettle' => 'sometimes|boolean',
            'has_wifi' => 'sometimes|boolean',
            'is_accessible' => 'sometimes|boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
        
        // Log des règles de validation
        \Log::info('Règles de validation :', $rules);
        
        // Validation
        $validator = \Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            // Log des erreurs de validation
            \Log::error('Erreurs de validation :', $validator->errors()->toArray());
            \Log::error('Données de la requête :', $request->all());
            
            // Renvoyer les erreurs au formulaire
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Si la validation réussit, on récupère les données validées
        $validated = $validator->validated();
        \Log::info('Données validées :', $validated);

        try {
            DB::beginTransaction();
            
            // Log avant la création de la chambre
            \Log::info('Création d\'une nouvelle chambre avec les données :', [
                'hotel_id' => $hotel->id,
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $validated['room_number'],
                'bed_type' => $validated['bed_type'] ?? 'non défini',
                'bed_count' => $validated['bed_count'] ?? 1,
                'max_occupancy' => $validated['max_occupancy'] ?? 1,
            ]);

            $roomData = [
                'hotel_id' => $hotel->id,
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $validated['room_number'],
                'floor' => $validated['floor'],
                'max_occupancy' => $validated['max_occupancy'],
                'price_per_night' => $validated['price_per_night'] * 100, // Convertir en centimes
                'size' => $validated['size'] ?? null,
                'view' => $validated['view'] ?? null,
                'bed_type' => $validated['bed_type'] ?? 'double', // Valeur par défaut ajoutée
                'bed_count' => $validated['bed_count'] ?? 1, // Valeur par défaut ajoutée
                'is_smoking_allowed' => $validated['is_smoking_allowed'] ?? false,
                'has_balcony' => $validated['has_balcony'] ?? false,
                'has_terrace' => $validated['has_terrace'] ?? false,
                'has_sea_view' => $validated['has_sea_view'] ?? false,
                'has_lake_view' => $validated['has_lake_view'] ?? false,
                'has_mountain_view' => $validated['has_mountain_view'] ?? false,
                'has_bathtub' => $validated['has_bathtub'] ?? false,
                'has_shower' => $validated['has_shower'] ?? true,
                'has_air_conditioning' => $validated['has_air_conditioning'] ?? false,
                'has_heating' => $validated['has_heating'] ?? false,
                'has_tv' => $validated['has_tv'] ?? false,
                'has_phone' => $validated['has_phone'] ?? false,
                'has_safe' => $validated['has_safe'] ?? false,
                'has_mini_bar' => $validated['has_mini_bar'] ?? false,
                'has_electric_kettle' => $validated['has_electric_kettle'] ?? false,
                'has_wifi' => $validated['has_wifi'] ?? false,
                'is_accessible' => $validated['is_accessible'] ?? false,
                'description' => $validated['description'] ?? null,
                'is_available' => true,
            ];
            
            // Log des données de la chambre avant création
            \Log::info('Données complètes de la chambre :', $roomData);
            
            try {
                // Get room type name from the ID
                $roomType = \App\Models\RoomType::findOrFail($validated['room_type_id']);
                
                // Prepare the room data for the database
                $roomData = [
                    'name' => $validated['name'] ?? 'Chambre ' . $validated['room_number'],
                    'type' => $roomType->name,
                    'price_per_night' => (float)$validated['price_per_night'],
                    'capacity' => (int)$request->input('capacity', 2),
                    'available' => true,
                    'hotel_id' => $hotel->id,
                    'description' => [
                        'size' => !empty($validated['size']) ? (int)$validated['size'] : null,
                        'view' => $validated['view'] ?? null,
                        'bed_type' => $validated['bed_type'],
                        'bed_count' => (int)$validated['bed_count'],
                        'max_occupancy' => (int)$validated['max_occupancy'],
                        'is_smoking_allowed' => $request->boolean('is_smoking_allowed', false),
                        'has_balcony' => $request->boolean('has_balcony', false),
                        'has_terrace' => $request->boolean('has_terrace', false),
                        'has_sea_view' => $request->boolean('has_sea_view', false),
                        'has_lake_view' => $request->boolean('has_lake_view', false),
                        'has_mountain_view' => $request->boolean('has_mountain_view', false),
                        'has_bathtub' => $request->boolean('has_bathtub', false),
                        'has_shower' => $request->boolean('has_shower', true),
                        'has_air_conditioning' => $request->boolean('has_air_conditioning', false),
                        'has_heating' => $request->boolean('has_heating', false),
                        'has_tv' => $request->boolean('has_tv', false),
                        'has_phone' => $request->boolean('has_phone', false),
                        'has_safe' => $request->boolean('has_safe', false),
                        'has_mini_bar' => $request->boolean('has_mini_bar', false),
                        'has_electric_kettle' => $request->boolean('has_electric_kettle', false),
                        'has_wifi' => $request->boolean('has_wifi', false),
                        'is_accessible' => $request->boolean('is_accessible', false),
                        'min_stay' => $request->filled('min_stay') ? (int)$request->input('min_stay') : 1,
                        'max_adults' => $request->filled('max_adults') ? (int)$request->input('max_adults') : 2,
                        'max_children' => $request->filled('max_children') ? (int)$request->input('max_children') : 0,
                        'room_number' => $validated['room_number'],
                        'floor' => (int)$validated['floor']
                    ]
                ];
                
                \Log::info('Attempting to create room with data:', $roomData);
                
                $room = new Room();
                $room->fill($roomData);
                $saved = $room->save();
                
                if (!$saved) {
                    throw new \Exception('Failed to save room to database');
                }
                
                \Log::info('Room created successfully with ID: ' . $room->id);
                
            } catch (\Exception $e) {
                \Log::error('Error creating room: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('error', 'Une erreur est survenue lors de la création de la chambre. Veuillez réessayer.')
                    ->withInput();
            }

            // Ajout des photos de la chambre
            if ($request->hasFile('photos')) {
                // Créer le dossier s'il n'existe pas
                $storagePath = storage_path('app/public/rooms/' . $room->id);
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $isFirst = true;
                foreach ($request->file('photos') as $photo) {
                    // Vérifier si le fichier est valide
                    if ($photo->isValid()) {
                        $path = $photo->store('rooms/' . $room->id, 'public');
                        $room->photos()->create([
                            'path' => $path,
                            'is_main' => $isFirst,
                        ]);
                        $isFirst = false;
                    }
                }
            }

            // Synchronisation des équipements
            if (!empty($validated['amenities'])) {
                $room->amenities()->sync($validated['amenities']);
            }
            
            DB::commit();
            
            return redirect()->route('hotels.rooms.index', ['hotel' => $hotel->id])
                ->with('success', 'La chambre a été créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la chambre.');
        }
    }

    /**
     * Affiche les détails d'une chambre
     */
    public function showRoom(Hotel $hotel, Room $room)
    {
        // $this->authorize('manage-hotel', $hotel);
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $room->load([
            'photos',
            'hotel',  // Load the hotel relationship first
            'bookings' => function($query) {
                $query->whereIn('status', ['confirmed'])
                    ->with('user')
                    ->orderBy('start_date', 'asc');
            },
            'reviews' => function($query) {
                $query->with('user')
                    ->orderBy('created_at', 'desc');
            }
        ]);
        
        // Manually load amenities through the hotel
        $room->setRelation('amenities', $room->hotel->amenities);

        // Calcul du taux d'occupation sur 6 mois
        $occupancyRate = $this->calculateRoomOccupancyRate($room);

        // Prochaines réservations
        $upcomingBookings = $room->bookings()
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();
            
        // Réservations récentes (30 derniers jours)
        $recentBookings = $room->bookings()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('hotel-manager.rooms.show', compact('hotel', 'room', 'occupancyRate', 'upcomingBookings', 'recentBookings'));
    }

    /**
     * Affiche le formulaire de modification d'une chambre
     */
    public function editRoom(Hotel $hotel, Room $room)
    {
        // $this->authorize('manage-hotel', $hotel);
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        // Charger les relations nécessaires
        $room->load(['photos', 'hotel.amenities']);
        
        // Récupérer la liste des types de chambres uniques
        $roomTypes = [
            'standard' => 'Standard',
            'deluxe' => 'Deluxe',
            'suite' => 'Suite',
            'family' => 'Familiale',
            'executive' => 'Exécutive'
        ];
        
        $amenities = Amenity::all(); // Récupérer tous les équipements sans filtre

        return view('hotel-manager.rooms.edit', compact('hotel', 'room', 'roomTypes', 'amenities'));
    }

    /**
     * Met à jour une chambre existante
     */
    public function updateRoom(Request $request, Hotel $hotel, Room $room)
    {
        // $this->authorize('manage-hotel', $hotel);
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . $room->id,
            'floor' => 'required|integer|min:-10|max:200',
            'max_occupancy' => 'required|integer|min:1|max:20',
            'price_per_night' => 'required|numeric|min:0.01|max:99999.99',
            'size' => 'nullable|integer|min:1',
            'view' => 'nullable|string|max:100',
            'bed_type' => 'required|string|max:100',
            'bed_count' => 'required|integer|min:1|max:10',
            'is_smoking_allowed' => 'boolean',
            'has_balcony' => 'boolean',
            'has_terrace' => 'boolean',
            'has_sea_view' => 'boolean',
            'has_lake_view' => 'boolean',
            'has_mountain_view' => 'boolean',
            'has_bathtub' => 'boolean',
            'has_shower' => 'boolean',
            'has_air_conditioning' => 'boolean',
            'has_heating' => 'boolean',
            'has_tv' => 'boolean',
            'has_phone' => 'boolean',
            'has_safe' => 'boolean',
            'has_mini_bar' => 'boolean',
            'has_electric_kettle' => 'boolean',
            'has_wifi' => 'boolean',
            'is_accessible' => 'boolean',
            'is_available' => 'boolean',
            'description' => 'nullable|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|max:5120',
            'delete_photos' => 'array',
            'delete_photos.*' => 'exists:photos,id',
        ]);

        try {
            DB::beginTransaction();

            // Mise à jour des informations de base
            $room->update([
                'room_type_id' => $validated['room_type_id'],
                'room_number' => $validated['room_number'],
                'floor' => $validated['floor'],
                'max_occupancy' => $validated['max_occupancy'],
                'price_per_night' => $validated['price_per_night'] * 100, // Convertir en centimes
                'size' => $validated['size'] ?? null,
                'view' => $validated['view'] ?? null,
                'bed_type' => $validated['bed_type'],
                'bed_count' => $validated['bed_count'],
                'is_smoking_allowed' => $validated['is_smoking_allowed'] ?? false,
                'has_balcony' => $validated['has_balcony'] ?? false,
                'has_terrace' => $validated['has_terrace'] ?? false,
                'has_sea_view' => $validated['has_sea_view'] ?? false,
                'has_lake_view' => $validated['has_lake_view'] ?? false,
                'has_mountain_view' => $validated['has_mountain_view'] ?? false,
                'has_bathtub' => $validated['has_bathtub'] ?? false,
                'has_shower' => $validated['has_shower'] ?? true,
                'has_air_conditioning' => $validated['has_air_conditioning'] ?? false,
                'has_heating' => $validated['has_heating'] ?? false,
                'has_tv' => $validated['has_tv'] ?? false,
                'has_phone' => $validated['has_phone'] ?? false,
                'has_safe' => $validated['has_safe'] ?? false,
                'has_mini_bar' => $validated['has_mini_bar'] ?? false,
                'has_electric_kettle' => $validated['has_electric_kettle'] ?? false,
                'has_wifi' => $validated['has_wifi'] ?? false,
                'is_accessible' => $validated['is_accessible'] ?? false,
                'is_available' => $validated['is_available'] ?? true,
                'description' => $validated['description'] ?? null,
            ]);

            // Ajout de nouvelles photos
            if ($request->hasFile('photos')) {
                $hasMainPhoto = $room->photos()->where('is_main', true)->exists();
                
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('rooms/' . $room->id, 'public');
                    $room->photos()->create([
                        'path' => $path,
                        'is_main' => !$hasMainPhoto, // Si pas de photo principale, la première devient principale
                    ]);
                    $hasMainPhoto = true;
                }
            }

            // Suppression des photos sélectionnées
            if (!empty($validated['delete_photos'])) {
                $photosToDelete = $room->photos()
                    ->whereIn('id', $validated['delete_photos'])
                    ->get();

                // Vérifier qu'on ne supprime pas la dernière photo principale
                $mainPhotoToDelete = $photosToDelete->where('is_main', true)->first();
                $remainingPhotos = $room->photos()->whereNotIn('id', $validated['delete_photos'])->count();

                if ($mainPhotoToDelete && $remainingPhotos > 0) {
                    // Définir une nouvelle photo comme principale
                    $newMainPhoto = $room->photos()
                        ->whereNotIn('id', $validated['delete_photos'])
                        ->first();
                    
                    if ($newMainPhoto) {
                        $newMainPhoto->update(['is_main' => true]);
                    }
                }

                // Supprimer les photos
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }

            // Synchronisation des équipements
            $room->amenities()->sync($validated['amenities'] ?? []);

            DB::commit();

            return redirect()->route('hotel-manager.rooms.show', ['hotel' => $hotel->id, 'room' => $room->id])
                ->with('success', 'Chambre mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la chambre.');
        }
    }

    /**
     * Supprime une chambre
     */
    public function destroyRoom(Hotel $hotel, Room $room)
    {
        // $this->authorize('manage-hotel', $hotel);
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        // Vérifier s'il y a des réservations futures
        $hasFutureBookings = $room->bookings()
            ->where('check_out', '>=', now())
            ->exists();

        if ($hasFutureBookings) {
            return back()->with('error', 'Impossible de supprimer cette chambre car elle a des réservations futures.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les photos du stockage
            foreach ($room->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            // Supprimer les relations
            $room->photos()->delete();
            $room->amenities()->detach();
            $room->unavailableDates()->delete();
            
            // Supprimer la chambre
            $room->delete();

            // Supprimer le dossier de la chambre s'il est vide
            $directory = 'rooms/' . $room->id;
            if (Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->deleteDirectory($directory);
            }

            DB::commit();

            return redirect()->route('hotel-manager.hotels.show', $hotel->id)
                ->with('success', 'Chambre supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la chambre.');
        }
    }

    /**
     * Affiche le calendrier des réservations d'un hôtel
     */
    public function calendar(Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);

        $rooms = $hotel->rooms()
            ->with(['bookings' => function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in']);
            }])
            ->get();

        $events = [];
        
        foreach ($rooms as $room) {
            foreach ($room->bookings as $booking) {
                $events[] = [
                    'title' => 'Chambre ' . $room->room_number . ' - ' . $booking->user->name,
                    'start' => $booking->start_date->toIso8601String(),
                    'end' => $booking->end_date->toIso8601String(),
                    'url' => route('hotels.bookings.show', ['hotel' => $hotel->id, 'booking' => $booking->id]),
                    'color' => $booking->status === 'checked_in' ? '#28a745' : '#007bff',
                    'extendedProps' => [
                        'status' => $booking->status,
                        'guest' => $booking->user->name,
                        'room' => $room->room_number,
                        'hotel' => $hotel->name,
          ]
                ];
            }
        }

        return view('hotel-manager.calendar', [
            'hotel' => $hotel,
            'events' => json_encode($events),
            'rooms' => $hotel->rooms()->pluck('room_number', 'id')
        ]);
    }

    /**
     * Récupère les événements du calendrier au format JSON
     */
    public function getCalendarEvents(Hotel $hotel, Request $request)
    {
        // $this->authorize('manage-hotel', $hotel);

        $start = Carbon::parse($request->start)->startOfDay();
        $end = Carbon::parse($request->end)->endOfDay();
        $roomId = $request->room_id;

        $query = $hotel->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->with(['room', 'user']);

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        $bookings = $query->get();

        $events = [];
        
        foreach ($bookings as $booking) {
            $events[] = [
                'id' => $booking->id,
                'title' => 'Chambre ' . $booking->room->room_number . ' - ' . $booking->user->name,
                'start' => $booking->start_date->toIso8601String(),
                'end' => $booking->end_date->toIso8601String(),
                'url' => route('hotel-manager.bookings.show', $booking->id),
                'color' => $booking->status === 'checked_in' ? '#28a745' : '#007bff',
                'extendedProps' => [
                    'status' => $booking->status,
                    'guest' => $booking->user->name,
                    'room' => $booking->room->room_number,
                    'phone' => $booking->user->phone,
                    'email' => $booking->user->email,
                    'total_amount' => number_format($booking->total_amount / 100, 2, ',', ' ') . ' €',
                    'created_at' => $booking->created_at->format('d/m/Y H:i'),
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Affiche la liste des réservations d'un hôtel
     */
    public function bookings(Hotel $hotel, Request $request)
    {
        // $this->authorize('manage-hotel', $hotel);

        $query = $hotel->bookings()
            ->with(['room', 'user'])
            ->latest();

        // Filtres
        if ($request->has('status') && in_array($request->status, ['pending', 'confirmed', 'cancelled', 'checked_in', 'checked_out', 'completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('room_id') && is_numeric($request->room_id)) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('id', $search);
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $rooms = $hotel->rooms()->pluck('room_number', 'id');

        return view('hotel-manager.bookings.index', compact('hotel', 'bookings', 'rooms'));
    }

    /**
     * Affiche les détails d'une réservation
     */
    public function showBooking(Hotel $hotel, HotelBooking $booking)
    {
        // $this->authorize('manage-hotel', $hotel);
        if ($booking->hotel_id !== $hotel->id) {
            abort(404);
        }

        $booking->load(['room', 'user', 'payments']);
        
        return view('hotel-manager.bookings.show', compact('hotel', 'booking'));
    }

    /**
     * Met à jour le statut d'une réservation
     */
    public function updateBookingStatus(Hotel $hotel, HotelBooking $booking, Request $request)
    {
        // $this->authorize('manage-hotel', $hotel);
        if ($booking->hotel_id !== $hotel->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,checked_in,checked_out,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $booking->status;
            $booking->status = $validated['status'];
            
            if (!empty($validated['notes'])) {
                $booking->admin_notes = $validated['notes'];
            }

            // Gestion des dates de check-in/out
            if ($validated['status'] === 'checked_in') {
                $booking->checked_in_at = now();
            } elseif ($validated['status'] === 'checked_out' || $validated['status'] === 'completed') {
                $booking->checked_out_at = now();
                
                // Si le paiement n'est pas complet, marquer comme complété
                if ($booking->balance_due <= 0) {
                    $booking->status = 'completed';
                    $booking->paid_at = now();
                }
            }

            $booking->save();

            // Envoyer une notification au client
            // TODO: Implémenter le système de notification
            // $booking->user->notify(new BookingStatusUpdated($booking, $oldStatus));

            DB::commit();

            return back()->with('success', 'Statut de la réservation mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du statut de la réservation.');
        }
    }

    /**
     * Affiche les rapports et statistiques d'un hôtel
     */
    public function reports(Hotel $hotel, Request $request)
    {
        // $this->authorize('manage-hotel', $hotel);

        $startDate = $request->input('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'monthly');

        // Validation des dates
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'report_type' => 'in:monthly,daily,room_type,source',
        ]);

        // Requête de base pour les réservations
        $query = $hotel->bookings()
            ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate);

        // Statistiques générales
        $stats = [
            'total_bookings' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total_price') / 100,
            'average_stay' => (clone $query)->get()->avg(function($booking) {
                return $booking->end_date->diffInDays($booking->start_date);
            }),
            'occupancy_rate' => $this->calculateOccupancyRate($hotel, $startDate, $endDate),
        ];

        // Données pour les graphiques
        $chartData = [];
        
        // Données mensuelles par défaut
        if ($reportType === 'monthly') {
            // Utiliser une sous-requête pour éviter la double jointure
            $monthlyData = DB::table('hotel_bookings')
                ->select(
                    DB::raw("TO_CHAR(hotel_bookings.start_date, 'YYYY-MM') as month"),
                    DB::raw('COUNT(*) as bookings_count'),
                    DB::raw('SUM(hotel_bookings.total_price) / 100 as total_revenue')
                )
                ->whereIn('hotel_bookings.room_id', function($query) use ($hotel) {
                    $query->select('id')
                          ->from('rooms')
                          ->where('hotel_id', $hotel->id);
                })
                ->whereIn('hotel_bookings.status', ['confirmed', 'checked_in', 'completed'])
                ->where('hotel_bookings.start_date', '>=', now()->subYear())
                ->groupBy(DB::raw("TO_CHAR(hotel_bookings.start_date, 'YYYY-MM')"))
                ->orderBy('month', 'asc')
                ->get();

            $chartData['labels'] = $monthlyData->pluck('month');
            $chartData['bookings'] = $monthlyData->pluck('bookings_count');
            $chartData['revenue'] = $monthlyData->pluck('total_revenue');
        }
        // Données par type de chambre
        elseif ($reportType === 'room_type') {
            $roomTypeData = $hotel->bookings()
                ->join('rooms', 'hotel_bookings.room_id', '=', 'rooms.id')
                ->select(
                    'rooms.type as room_type',
                    DB::raw('COUNT(*) as bookings_count'),
                    DB::raw('SUM(hotel_bookings.total_price) / 100 as total_revenue')
                )
                ->whereIn('hotel_bookings.status', ['confirmed', 'checked_in', 'completed'])
                ->where('hotel_bookings.start_date', '>=', $startDate)
                ->where('hotel_bookings.start_date', '<=', $endDate)
                ->groupBy('rooms.type')
                ->orderBy('room_type')
                ->get();

            $chartData['labels'] = $roomTypeData->pluck('room_type');
            $chartData['bookings'] = $roomTypeData->pluck('bookings_count');
            $chartData['revenue'] = $roomTypeData->pluck('total_revenue');
        }
        // Source de réservation
        elseif ($reportType === 'source') {
            $sourceData = $hotel->bookings()
                ->select(
                    'source',
                    DB::raw('COUNT(*) as bookings_count'),
                    DB::raw('SUM(total_amount) / 100 as total_revenue')
                )
                ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
                ->where('check_in', '>=', $startDate)
                ->where('check_in', '<=', $endDate)
                ->groupBy('source')
                ->get();

            $chartData['labels'] = $sourceData->pluck('source');
            $chartData['bookings'] = $sourceData->pluck('bookings_count');
            $chartData['revenue'] = $sourceData->pluck('total_revenue');
        }

        // Dernières réservations
        $recentBookings = $hotel->bookings()
            ->with(['room', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Chambres les plus populaires
        $popularRooms = $hotel->rooms()
            ->withCount(['bookings' => function($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['confirmed', 'checked_in', 'completed'])
                    ->where('start_date', '>=', $startDate)
                    ->where('start_date', '<=', $endDate);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // Convertir les dates en instances Carbon si ce ne sont pas déjà des objets
        $startDate = $startDate instanceof \Carbon\Carbon ? $startDate : \Carbon\Carbon::parse($startDate);
        $endDate = $endDate instanceof \Carbon\Carbon ? $endDate : \Carbon\Carbon::parse($endDate);

        return view('hotel-manager.reports.index', [
            'hotel' => $hotel,
            'stats' => $stats,
            'chartData' => $chartData,
            'recentBookings' => $recentBookings,
            'popularRooms' => $popularRooms,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => $reportType,
        ]);
    }

    /**
     * Exporte les rapports au format CSV ou Excel
     */
    public function exportReports(Hotel $hotel, Request $request)
    {
        // $this->authorize('manage-hotel', $hotel);

        $validated = $request->validate([
            'type' => 'required|in:bookings,revenue,guests',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);

        $startDate = $validated['start_date'] ?? now()->subYear()->format('Y-m-d');
        $endDate = $validated['end_date'] ?? now()->format('Y-m-d');

        $data = [];
        $filename = '';
        $headers = [];

        switch ($validated['type']) {
            case 'bookings':
                $data = $hotel->bookings()
                    ->with(['room', 'user'])
                    ->whereBetween('check_in', [$startDate, $endDate])
                    ->get()
                    ->map(function($booking) {
                        return [
                            'ID' => $booking->id,
                            'Date de réservation' => $booking->created_at->format('d/m/Y H:i'),
                            'Client' => $booking->user->name,
                            'Email' => $booking->user->email,
                            'Téléphone' => $booking->user->phone,
                            'Chambre' => $booking->room->room_number,
                            'Type de chambre' => $booking->room->type,
                            'Arrivée' => $booking->check_in->format('d/m/Y'),
                            'Départ' => $booking->check_out->format('d/m/Y'),
                            'Nuits' => $booking->check_out->diffInDays($booking->check_in),
                            'Prix/nuit' => number_format($booking->room_price / 100, 2, ',', ' ') . ' €',
                            'Total' => number_format($booking->total_amount / 100, 2, ',', ' ') . ' €',
                            'Statut' => $this->getStatusLabel($booking->status),
                            'Source' => $booking->source,
                        ];
                    });
                $filename = 'reservations-' . now()->format('Y-m-d');
                break;

            case 'revenue':
                $data = $hotel->bookings()
                    ->select(
                        DB::raw("TO_CHAR(start_date, 'YYYY-MM') as month"),
                        DB::raw('COUNT(*) as bookings_count'),
                        DB::raw('SUM(total_price) / 100 as total_revenue'),
                        DB::raw('AVG(total_price) / 100 as average_booking_value')
                    )
                    ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->groupBy(DB::raw("TO_CHAR(start_date, 'YYYY-MM')"))
                    ->orderBy('month')
                    ->get()
                    ->map(function($item) {
                        return [
                            'Mois' => Carbon::createFromFormat('Y-m', $item->month)->format('m/Y'),
                            'Réservations' => $item->bookings_count,
                            'Chiffre d\'affaires' => number_format($item->total_revenue, 2, ',', ' ') . ' €',
                            'Panier moyen' => number_format($item->average_booking_value, 2, ',', ' ') . ' €',
                        ];
                    });
                $filename = 'revenus-' . now()->format('Y-m-d');
                break;

            case 'guests':
                $data = $hotel->bookings()
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'users.phone',
                        'users.country',
                        DB::raw('COUNT(*) as total_stays'),
                        DB::raw('SUM(DATEDIFF(check_out, check_in)) as total_nights'),
                        DB::raw('SUM(total_amount) / 100 as total_spent')
                    )
                    ->join('users', 'hotel_bookings.user_id', '=', 'users.id')
                    ->whereIn('hotel_bookings.status', ['confirmed', 'checked_in', 'completed'])
                    ->whereBetween('hotel_bookings.check_in', [$startDate, $endDate])
                    ->groupBy('users.id', 'users.name', 'users.email', 'users.phone', 'users.country')
                    ->orderBy('total_spent', 'desc')
                    ->get()
                    ->map(function($item, $index) {
                        return [
                            '#' => $index + 1,
                            'Nom' => $item->name,
                            'Email' => $item->email,
                            'Téléphone' => $item->phone,
                            'Pays' => $item->country,
                            'Séjours' => $item->total_stays,
                            'Nuits' => $item->total_nights,
                            'Dépenses totales' => number_format($item->total_spent, 2, ',', ' ') . ' €',
                        ];
                    });
                $filename = 'clients-' . now()->format('Y-m-d');
                break;
        }

        if ($validated['format'] === 'csv') {
            $headers = $data->isNotEmpty() ? array_keys($data->first()) : [];
            
            $callback = function() use ($data, $headers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers, ';');
                
                foreach ($data as $row) {
                    // Nettoyer les valeurs pour le CSV
                    $row = array_map(function($value) {
                        // Supprimer les séparateurs de milliers et remplacer la virgule par un point pour les nombres
                        if (is_string($value) && preg_match('/^[0-9]+(,[0-9]+)€?$/', $value)) {
                            return str_replace([',', '€', ' '], ['.', '', ''], $value);
                        }
                        return $value;
                    }, $row);
                    
                    fputcsv($file, $row, ';');
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ]);
        } else {
            // Pour Excel, on utilise la bibliothèque maatwebsite/excel si elle est installée
            // Sinon, on renvoie une réponse JSON avec un message d'erreur
            if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
                $export = new \App\Exports\GenericExport($data->toArray(), $headers);
                return \Maatwebsite\Excel\Facades\Excel::download($export, $filename . '.xlsx');
            } else {
                return response()->json([
                    'error' => 'La fonctionnalité d\'export Excel nécessite le package maatwebsite/excel.',
                    'data' => $data
                ]);
            }
        }
    }

    /**
     * Affiche les paramètres de l'hôtel
     */
    public function settings(Hotel $hotel)
    {
        // $this->authorize('manage-hotel', $hotel);
        
        $hotel->load(['photos', 'amenities', 'rules']);
        $amenities = Amenity::all();
        $rules = StayRule::all();
        
        return view('hotel-manager.settings', compact('hotel', 'amenities', 'rules'));
    }

    /**
     * Met à jour les paramètres de l'hôtel
     */
    public function updateSettings(Hotel $hotel, Request $request)
    {
        // $this->authorize('manage-hotel', $hotel);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'website' => 'nullable|url',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i|after:check_in_time',
            'cancellation_policy' => 'required|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'rules' => 'array',
            'rules.*' => 'exists:rules,name',
            'main_photo' => 'nullable|image|max:5120',
            'photos.*' => 'image|max:5120',
            'delete_photos' => 'array',
            'delete_photos.*' => 'exists:photos,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'min_stay' => 'nullable|integer|min:1',
            'max_stay' => 'nullable|integer|min:1|gt:min_stay',
            'advance_notice' => 'nullable|integer|min:0',
            'prepayment_percent' => 'nullable|integer|min:0|max:100',
            'free_cancellation_days' => 'nullable|integer|min:0',
            'check_in_instructions' => 'nullable|string',
            'check_out_instructions' => 'nullable|string',
            'pets_policy' => 'nullable|string',
            'child_policy' => 'nullable|string',
            'payment_methods' => 'array',
            'languages' => 'array',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|timezone',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mise à jour des informations de base
            $hotel->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'website' => $validated['website'] ?? null,
                'check_in_time' => $validated['check_in_time'],
                'check_out_time' => $validated['check_out_time'],
                'cancellation_policy' => $validated['cancellation_policy'],
                'is_active' => $validated['is_active'] ?? true,
                'is_featured' => $validated['is_featured'] ?? false,
                'min_stay' => $validated['min_stay'] ?? null,
                'max_stay' => $validated['max_stay'] ?? null,
                'advance_notice' => $validated['advance_notice'] ?? 0,
                'prepayment_percent' => $validated['prepayment_percent'] ?? 0,
                'free_cancellation_days' => $validated['free_cancellation_days'] ?? 0,
                'check_in_instructions' => $validated['check_in_instructions'] ?? null,
                'check_out_instructions' => $validated['check_out_instructions'] ?? null,
                'pets_policy' => $validated['pets_policy'] ?? null,
                'child_policy' => $validated['child_policy'] ?? null,
                'payment_methods' => $validated['payment_methods'] ?? [],
                'languages' => $validated['languages'] ?? ['fr'],
                'currency' => $validated['currency'],
                'timezone' => $validated['timezone'],
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);
            
            // Gestion de la photo principale
            if ($request->hasFile('main_photo')) {
                // Supprimer l'ancienne photo principale
                $oldMainPhoto = $hotel->photos()->where('is_main', true)->first();
                if ($oldMainPhoto) {
                    Storage::disk('public')->delete($oldMainPhoto->path);
                    $oldMainPhoto->delete();
                }
                
                // Ajouter la nouvelle photo principale
                $path = $request->file('main_photo')->store('hotels/' . $hotel->id, 'public');
                $hotel->photos()->create([
                    'path' => $path,
                    'is_main' => true,
                ]);
            }
            
            // Ajout de nouvelles photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('hotels/' . $hotel->id, 'public');
                    $hotel->photos()->create([
                        'path' => $path,
                        'is_main' => false,
                    ]);
                }
            }
            
            // Suppression des photos sélectionnées
            if (!empty($validated['delete_photos'])) {
                $photosToDelete = $hotel->photos()
                    ->whereIn('id', $validated['delete_photos'])
                    ->where('is_main', false)
                    ->get();
                
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }
            
            // Synchronisation des équipements
            $hotel->amenities()->sync($validated['amenities'] ?? []);
            
            // Gestion des règles
            $ruleIds = [];
            if (!empty($validated['rules'])) {
                foreach ($validated['rules'] as $ruleName) {
                    // Vérifier si la règle existe déjà
                    $rule = StayRule::firstOrCreate(
                        ['name' => $ruleName],
                        ['name' => $ruleName]
                    );
                    $ruleIds[] = $rule->id;
                }
            }
            
            // Synchronisation des règles
            $hotel->rules()->sync($ruleIds);
            
            DB::commit();
            
            return redirect()->route('hotel-manager.settings', $hotel->id)
                ->with('success', 'Paramètres mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour des paramètres.');
        }
    }
    
    /**
     * Calcule le taux d'occupation d'un hôtel sur une période donnée
     */
    protected function calculateOccupancyRate(Hotel $hotel, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : now()->endOfMonth();
        
        $totalRooms = $hotel->rooms()->count();
        if ($totalRooms === 0) {
            return 0;
        }
        
        $days = $startDate->diffInDays($endDate) + 1;
        $totalRoomNights = $totalRooms * $days;
        
        // Récupérer toutes les réservations pertinentes
        $bookings = $hotel->bookings()
            ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get();
        
        // Calculer les nuitées réservées en PHP
        $bookedRoomNights = 0;
        foreach ($bookings as $booking) {
            $checkIn = Carbon::parse($booking->start_date);
            $checkOut = Carbon::parse($booking->end_date);
            
            // Ajuster les dates pour ne considérer que la période demandée
            $periodStart = $checkIn->isBefore($startDate) ? $startDate : $checkIn;
            $periodEnd = $checkOut->isAfter($endDate) ? $endDate : $checkOut;
            
            // Ajouter le nombre de nuits pour cette réservation
            $bookedRoomNights += $periodStart->diffInDays($periodEnd);
        }
        
        return $totalRoomNights > 0 ? ($bookedRoomNights / $totalRoomNights) * 100 : 0;
    }
    
    /**
     * Calcule le taux d'occupation d'une chambre sur une période donnée
     */
    protected function calculateRoomOccupancyRate(Room $room, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : now()->endOfMonth();
        
        $days = $startDate->diffInDays($endDate) + 1;
        
        $bookedDays = $room->bookings()
            ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->select(DB::raw("SUM(
                (LEAST(DATE(?), DATE(end_date)) - GREATEST(DATE(?), DATE(start_date)) + 1)
            ) as booked_days"))
            ->addBinding([$endDate, $startDate], 'select')
            ->value('booked_days') ?? 0;
        
        return $days > 0 ? ($bookedDays / $days) * 100 : 0;
    }
    
    /**
     * Convertit un statut en libellé lisible
     */
    protected function getStatusLabel($status)
    {
        $statuses = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'checked_in' => 'En cours',
            'checked_out' => 'Terminée',
            'completed' => 'Terminée',
            'no_show' => 'No Show',
        ];
        
        return $statuses[$status] ?? $status;
    }

    /**
     * implementation de la methode tooglestatus
     */
    public function toggleStatus(Hotel $hotel)
    {
        $hotel->is_active = !$hotel->is_active;
        $hotel->save();
        return redirect()->back()->with('success', 'Statut mis à jour avec succès.');
    }
    /**
     * Toggle the featured status of a hotel
     */
    protected function toggleFeatured(Hotel $hotel)
    {
        $hotel->is_featured = !$hotel->is_featured;
        $hotel->save();
        return redirect()->back()->with('success', 'Statut vedette mis à jour avec succès.');
    }
    
    /**
     * Bascule la disponibilité d'une chambre
     */
    public function toggleRoomAvailability(Hotel $hotel, Room $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            abort(404);
        }

        $room->update([
            'available' => !$room->available
        ]);

        return back()->with('success', 'La disponibilité de la chambre a été mise à jour avec succès.');
    }
}
