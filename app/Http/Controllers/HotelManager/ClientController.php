<?php

namespace App\Http\Controllers\HotelManager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Models\HotelBooking;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
// use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients de l'hôtel
     */
    public function index(Hotel $hotel)
    {
        // $this->authorize('viewAny', [User::class, $hotel]);
        
        // Récupérer tous les utilisateurs avec le rôle 'tourist'
        $clients = User::where('role', 'tourist')
            ->withCount(['hotelBookings as hotel_bookings_count' => function($query) use ($hotel) {
                $query->whereHas('room', function($q) use ($hotel) {
                    $q->where('hotel_id', $hotel->id);
                });
            }])
            ->orderBy('last_name')
            ->paginate(15);

        return view('hotel-manager.clients.index', [
            'hotel' => $hotel->load('manager'),
            'clients' => $clients,
        ]);
    }

    /**
     * Afficher le formulaire de création d'un client
     */
    public function create(Hotel $hotel)
    {
        // $this->authorize('create', [User::class, $hotel]);
        
        return view('hotel-manager.clients.create', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * Enregistrer un nouveau client
     */
    public function store(Request $request, Hotel $hotel)
    {
        // $this->authorize('create', [User::class, $hotel]);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'notes' => 'nullable|string',
        ]);

        // Créer le mot de passe par défaut (peut être modifié par l'utilisateur plus tard via un email de réinitialisation)
        $password = Hash::make(Str::random(12));
        
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $password,
            'role' => 'tourist',
            'is_active' => true,
        ]);

        // Créer le profil utilisateur
        $user->profile()->create([
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('hotels.clients.index', $hotel->id)
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Afficher les détails d'un client
     */
    public function show(Hotel $hotel, User $client)
    {
        // $this->authorize('view', [$client, $hotel]);
        
        // Vérifier que le client a bien effectué des réservations dans cet hôtel
        $hasBookings = $client->hotelBookings()
            ->whereHas('room', function($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->exists();
            
        if (!$hasBookings) {
            abort(404);
        }

        // Récupérer les réservations du client dans cet hôtel
        $bookings = $client->hotelBookings()
            ->with(['room', 'room.roomType', 'payments'])
            ->whereHas('room', function($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->latest()
            ->paginate(10);

        return view('hotel-manager.clients.show', [
            'hotel' => $hotel,
            'client' => $client->load('profile'),
            'bookings' => $bookings,
        ]);
    }

    /**
     * Afficher le formulaire de modification d'un client
     */
    public function edit(Hotel $hotel, User $client)
    {
        // $this->authorize('update', [$client, $hotel]);
        
        // Vérifier que le client a bien effectué des réservations dans cet hôtel
        $hasBookings = $client->hotelBookings()
            ->whereHas('room', function($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
            ->exists();
            
        if (!$hasBookings) {
            abort(404);
        }

        return view('hotel-manager.clients.edit', [
            'hotel' => $hotel,
            'client' => $client->load('profile'),
        ]);
    }

    /**
     * Mettre à jour les informations d'un client
     */
    public function update(Request $request, Hotel $hotel, User $client)
    {
        // $this->authorize('update', [$client, $hotel]);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($client->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'notes' => 'nullable|string',
        ]);

        // Mettre à jour les informations de base de l'utilisateur
        $client->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Mettre à jour ou créer le profil utilisateur
        $client->profile()->updateOrCreate(
            ['user_id' => $client->id],
            [
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'country' => $validated['country'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('hotel-manager.clients.show', [$hotel->id, $client->id])
            ->with('success', 'Informations du client mises à jour avec succès.');
    }
}
