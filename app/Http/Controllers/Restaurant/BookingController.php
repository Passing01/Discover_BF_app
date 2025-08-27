<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\Booking;
use App\Models\Restaurant\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    protected $restaurant;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:restaurant_manager');
        $this->middleware(function ($request, $next) {
            $this->restaurant = Auth::user()->restaurant;
            if (!$this->restaurant) {
                abort(403, 'Aucun restaurant associé à ce compte.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = $this->restaurant->bookings()->with(['table', 'user']);
        
        // Filtrage par statut
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filtrage par date
        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('booking_date', $request->date);
        } else {
            $query->whereDate('booking_date', '>=', now()->toDateString());
        }
        
        // Recherche
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        
        $bookings = $query->latest('booking_date')->paginate(15);
        
        // Données pour le formulaire de création
        $timeSlots = $this->generateTimeSlots();
        $maxCapacity = $this->restaurant->tables()->max('capacity') ?? 10;
        
        return Inertia::render('Restaurant/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status', 'date', 'search']),
            'timeSlots' => $timeSlots,
            'maxCapacity' => $maxCapacity,
        ]);
    }

    public function calendar()
    {
        $events = $this->restaurant->bookings()
            ->select('id', 'customer_name', 'booking_date', 'booking_time', 'people', 'status')
            ->where('booking_date', '>=', now()->subDays(7)->toDateString())
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->customer_name . ' - ' . $booking->people . 'p',
                    'start' => $booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time,
                    'end' => $booking->booking_date->format('Y-m-d') . ' ' . 
                             Carbon::parse($booking->booking_time)->addHours(2)->format('H:i'),
                    'className' => 'booking-status-' . $booking->status,
                    'extendedProps' => [
                        'status' => $booking->status,
                        'people' => $booking->people,
                    ]
                ];
            });
            
        return Inertia::render('Restaurant/Bookings/Calendar', [
            'events' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'people' => 'required|integer|min:1|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'special_requests' => 'nullable|string|max:1000',
            'table_id' => 'nullable|exists:tables,id',
        ]);
        
        // Vérifier la disponibilité
        $bookingTime = Carbon::parse($validated['booking_date'] . ' ' . $validated['booking_time']);
        
        if ($this->isFullyBooked($validated['people'], $bookingTime)) {
            return back()->with('error', 'Désolé, plus de disponibilité pour cette date et heure.');
        }
        
        try {
            $booking = $this->restaurant->bookings()->create([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'people' => $validated['people'],
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'special_requests' => $validated['special_requests'] ?? null,
                'status' => 'confirmed',
                'table_id' => $validated['table_id'] ?? null,
                'user_id' => Auth::id(),
            ]);
            
            // Envoyer une notification au client si email fourni
            if ($booking->customer_email) {
                // TODO: Implémenter l'envoi d'email
            }
            
            return redirect()->route('restaurant.bookings.index')
                ->with('success', 'La réservation a été enregistrée avec succès.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la réservation.');
        }
    }

    public function updateStatus(Booking $booking, Request $request)
    {
        $this->authorize('update', $booking);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no_show',
            'cancellation_reason' => 'required_if:status,cancelled|string|max:255',
        ]);
        
        try {
            $booking->update([
                'status' => $validated['status'],
                'cancellation_reason' => $validated['cancellation_reason'] ?? null,
                'cancelled_at' => $validated['status'] === 'cancelled' ? now() : null,
            ]);
            
            // Envoyer une notification de mise à jour si nécessaire
            if (in_array($validated['status'], ['confirmed', 'cancelled'])) {
                // TODO: Implémenter la notification
            }
            
            return back()->with('success', 'Le statut de la réservation a été mis à jour.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        try {
            $booking->delete();
            return back()->with('success', 'La réservation a été supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
    
    protected function generateTimeSlots()
    {
        $slots = [];
        $start = Carbon::parse($this->restaurant->opening_time ?? '11:00');
        $end = Carbon::parse($this->restaurant->closing_time ?? '23:00');
        
        while ($start->lessThan($end)) {
            $time = $start->format('H:i');
            $slots[$time] = $time;
            $start->addMinutes(30);
        }
        
        return $slots;
    }
    
    protected function isFullyBooked($people, Carbon $datetime)
    {
        // Vérifier la capacité du restaurant pour l'heure demandée
        $endTime = (clone $datetime)->addHours(2);
        
        $currentBookings = $this->restaurant->bookings()
            ->where(function($query) use ($datetime, $endTime) {
                $query->whereBetween('booking_time', [
                    $datetime->format('H:i:s'),
                    $endTime->format('H:i:s')
                ]);
            })
            ->whereDate('booking_date', $datetime->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'seated'])
            ->sum('people');
            
        $availableCapacity = $this->restaurant->capacity - $currentBookings;
        
        return $availableCapacity < $people;
    }
}
