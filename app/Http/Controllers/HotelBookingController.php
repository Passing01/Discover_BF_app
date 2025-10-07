<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\HotelBooking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HotelBookingController extends Controller
{
    /**
     * Affiche la liste des réservations pour un hôtel
     */
    public function index(Hotel $hotel)
    {
        // $this->authorize('viewAny', [HotelBooking::class, $hotel]);
        
        $bookings = $hotel->bookings()
            ->with([
                'room' => function($query) {
                    $query->with(['photos', 'roomType']);
                },
                'user',
                'payment'
            ])
            ->latest('start_date')
            ->paginate(15);
            
        return view('hotel-manager.bookings.index', compact('hotel', 'bookings'));
    }

    /**
     * Affiche le formulaire de création d'une réservation
     */
    public function create(Request $request, Hotel $hotel)
    {
        // $this->authorize('create', [HotelBooking::class, $hotel]);
        
        // Récupérer les chambres disponibles
        $rooms = $hotel->availableRooms()->get();
            
        // Grouper les chambres par leur type
        $groupedRooms = $rooms->groupBy('type');
        
        // Si aucune chambre n'est disponible, on crée une collection vide
        if ($groupedRooms->isEmpty()) {
            $groupedRooms = collect();
        }
            
        // Récupérer les types de chambres pour le formulaire
        $roomTypes = \App\Models\RoomType::all();
        
        // Récupérer les utilisateurs avec le rôle 'tourist' pour la sélection du client
        $users = \App\Models\User::where('role', 'tourist')
            ->orderBy('last_name')
            ->get();
            
        // Récupérer les informations du client si un ID est fourni dans la requête
        $selectedUser = null;
        if ($request->has('user_id')) {
            $selectedUser = \App\Models\User::with('profile')->find($request->user_id);
        }

        return view('hotel-manager.bookings.create', [
            'hotel' => $hotel,
            'rooms' => $rooms, // Collection de chambres non groupées pour le formulaire
            'groupedRooms' => $groupedRooms, // Chambres groupées par type (au cas où)
            'roomTypes' => $roomTypes,
            'users' => $users,
            'selectedUser' => $selectedUser
        ]);
    }

    /**
     * Enregistre une nouvelle réservation
     */
    public function store(Request $request, Hotel $hotel)
    {
        // $this->authorize('create', [HotelBooking::class, $hotel]);
        
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id', 
                Rule::exists('rooms', 'id')->where('hotel_id', $hotel->id)
            ],
            'user_id' => 'required|exists:users,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1|max:10',
            'children' => 'nullable|integer|min:0|max:10',
            'special_requests' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);
        
        // Vérifier la disponibilité de la chambre
        $room = Room::findOrFail($validated['room_id']);
        
        if (!$room->isAvailable($validated['check_in'], $validated['check_out'])) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Cette chambre n\'est pas disponible pour les dates sélectionnées.']);
        }
        
        // Calculer le nombre de nuits et le montant total
        $startDate = Carbon::parse($validated['check_in']);
        $endDate = Carbon::parse($validated['check_out']);
        $nights = $startDate->diffInDays($endDate);
        $totalAmount = $room->price_per_night * $nights;
        
        // Créer d'abord le paiement
        $payment = Payment::create([
            'user_id' => $validated['user_id'],
            'amount' => $totalAmount,
            'currency' => 'XOF',
            'payment_method' => $validated['payment_method'],
            'status' => $validated['payment_status'] === 'paid' ? 'completed' : 'pending',
            'reference' => 'PAY-' . strtoupper(Str::random(10)),
            'payment_date' => now(),
        ]);

        // Créer la réservation avec l'ID du paiement
        $booking = $hotel->bookings()->create([
            'room_id' => $validated['room_id'],
            'user_id' => $validated['user_id'],
            'payment_id' => $payment->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'confirmed',
            'total_price' => $totalAmount,
            'reference' => 'HOTEL-' . strtoupper(Str::random(8)),
        ]);

        // Créer une transaction pour le paiement
        $payment->transactions()->create([
            'type' => 'booking',
            'description' => 'Réservation de chambre #' . $booking->reference,
        ]);
        
        
        return redirect()
            ->route('hotel-manager.hotels.bookings.show', [$hotel, $booking])
            ->with('success', 'Réservation créée avec succès.');
    }

    /**
     * Affiche les détails d'une réservation
     */
    public function show(Hotel $hotel, HotelBooking $booking)
    {
        // $this->authorize('view', [$booking, $hotel]);
        
        $booking->load(['room', 'user', 'payment']);
        
        return view('hotel-manager.bookings.show', compact('hotel', 'booking'));
    }

    /**
     * Met à jour le statut d'une réservation
     */
    public function updateStatus(Request $request, Hotel $hotel, HotelBooking $booking)
    {
        // $this->authorize('update', [$booking, $hotel]);
        
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,checked_in,checked_out,cancelled,no_show'],
            'cancellation_reason' => ['required_if:status,cancelled', 'string', 'max:500'],
        ]);
        
        $booking->update([
            'status' => $validated['status'],
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
            'cancelled_at' => $validated['status'] === 'cancelled' ? now() : null,
        ]);
        
        // Si la réservation est annulée, libérer la chambre
        if ($validated['status'] === 'cancelled') {
            // Logique pour libérer la chambre si nécessaire
        }
        
        return back()->with('success', 'Statut de la réservation mis à jour avec succès.');
    }

    /**
     * Annule une réservation
     */
    public function cancel(Hotel $hotel, HotelBooking $booking)
    {
        // $this->authorize('cancel', [$booking, $hotel]);
        
        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Cette réservation est déjà annulée.');
        }
        
        if ($booking->status === 'checked_out') {
            return back()->with('error', 'Impossible d\'annuler une réservation déjà terminée.');
        }
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);
        
        // Remboursement si nécessaire
        if ($booking->payment && $booking->payment->status === 'paid') {
            // Logique de remboursement
        }
        
        return back()->with('success', 'Réservation annulée avec succès.');
    }

    /**
     * Exporte les réservations au format CSV
     */
    public function export(Hotel $hotel)
    {
        // $this->authorize('viewAny', [HotelBooking::class, $hotel]);
        
        $bookings = $hotel->bookings()
            ->with(['room', 'user'])
            ->latest('check_in')
            ->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reservations-' . now()->format('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'Référence',
                'Chambre',
                'Client',
                'Email',
                'Téléphone',
                'Date d\'arrivée',
                'Date de départ',
                'Nuits',
                'Adultes',
                'Enfants',
                'Montant total',
                'Statut',
                'Date de réservation'
            ]);
            
            // Données
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->room->name,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->user->phone,
                    $booking->check_in->format('d/m/Y'),
                    $booking->check_out->format('d/m/Y'),
                    $booking->check_in->diffInDays($booking->check_out),
                    $booking->adults,
                    $booking->children,
                    number_format($booking->total_amount, 0, ',', ' ') . ' FCFA',
                    $this->getStatusLabel($booking->status),
                    $booking->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Convertit un statut en libellé lisible
     */
    protected function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'checked_in' => 'En cours',
            'checked_out' => 'Terminée',
            'cancelled' => 'Annulée',
            'no_show' => 'Non présenté',
        ];
        
        return $labels[$status] ?? $status;
    }
}
