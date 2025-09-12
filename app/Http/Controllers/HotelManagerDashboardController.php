<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Ad;
use App\Models\HotelBooking;
use App\Models\Amenity;
use App\Models\StayRule;
use App\Models\HotelPhoto;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\Geocoding\NominatimGeocoder;

class HotelManagerDashboardController extends Controller
{
    protected $user;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $hotels = Hotel::where('manager_id', $this->user->id)
            ->withCount(['rooms', 'bookings as active_bookings' => function($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            }])
            ->orderByDesc('created_at')
            ->paginate(10);
            
        // Statistiques pour le tableau de bord
        $hotelIds = $hotels->pluck('id');
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $stats = [
            'total_hotels' => $hotels->total(),
            'total_rooms' => Room::whereIn('hotel_id', $hotelIds)->count(),
            'active_bookings' => HotelBooking::whereHas('room', function($query) use ($hotelIds) {
                    $query->whereIn('hotel_id', $hotelIds);
                })
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'revenue' => HotelBooking::whereHas('room', function($query) use ($hotelIds) {
                    $query->whereIn('hotel_id', $hotelIds);
                })
                ->where('status', 'completed')
                ->sum('total_price'),
            'monthly_revenue' => HotelBooking::whereHas('room', function($query) use ($hotelIds) {
                    $query->whereIn('hotel_id', $hotelIds);
                })
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_price')
        ];
        
        // Dernières réservations
        $recentBookings = HotelBooking::whereHas('room', function($query) use ($hotelIds) {
                $query->whereIn('hotel_id', $hotelIds);
            })
            ->with([
                'room.hotel', 
                'user',
                'room' => function($query) {
                    $query->select('id', 'hotel_id', 'name');
                },
                'room.hotel' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->select('id', 'room_id', 'user_id', 'start_date', 'end_date', 'status', 'total_price', 'created_at')
            ->latest()
            ->take(5)
            ->get();
            
        // Chambres avec statut
        $rooms = Room::whereIn('hotel_id', $hotelIds)
            ->with(['hotel', 'bookings' => function($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            }])
            ->get()
            ->map(function($room) {
                $room->status = $room->available ? 'available' : 'occupied';
                $room->next_available = $room->bookings->sortBy('end_date')->first()->end_date ?? null;
                return $room;
            });

        return view('hotel-manager.dashboard', compact('hotels', 'stats', 'recentBookings', 'rooms'));
    }
    
    public function bookings(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');
        
        $query = HotelBooking::whereHas('hotel', function($q) {
                $q->where('manager_id', $this->user->id);
            })
            ->with(['hotel', 'room', 'user']);
            
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }
        
        $bookings = $query->latest()->paginate(15);
        
        return view('hotel-manager.bookings.index', compact('bookings', 'status'));
    }
    
    public function showBooking(HotelBooking $booking)
    {
        $this->authorize('view', $booking);
        return view('hotel-manager.bookings.show', compact('booking'));
    }
    
    public function updateBookingStatus(Request $request, HotelBooking $booking)
    {
        $this->authorize('update', $booking);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $oldStatus = $booking->status;
        $booking->update([
            'status' => $request->status,
            'notes' => $request->notes ?: $booking->notes
        ]);
        
        // Envoyer une notification au client si le statut a changé
        if ($oldStatus !== $booking->status) {
            // TODO: Implémenter la notification
        }
        
        return redirect()->back()->with('success', 'Statut de la réservation mis à jour.');
    }
    
    public function calendar()
    {
        $hotels = Hotel::where('manager_id', $this->user->id)
            ->with(['rooms' => function($query) {
                $query->with(['bookings' => function($q) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                }]);
            }])
            ->get();
            
        $events = [];
        foreach ($hotels as $hotel) {
            foreach ($hotel->rooms as $room) {
                foreach ($room->bookings as $booking) {
                    $events[] = [
                        'id' => $booking->id,
                        'title' => "{$room->name} - {$booking->guest_name}",
                        'start' => $booking->check_in->toIsoString(),
                        'end' => $booking->check_out->toIsoString(),
                        'url' => route('hotel-manager.bookings.show', $booking),
                        'color' => $booking->status === 'confirmed' ? '#28a745' : 
                                  ($booking->status === 'pending' ? '#ffc107' : '#dc3545'),
                        'extendedProps' => [
                            'status' => $booking->status,
                            'room' => $room->name,
                            'guest' => $booking->guest_name,
                            'hotel' => $hotel->name
                        ]
                    ];
                }
            }
        }
        
        return view('hotel-manager.calendar', compact('events'));
    }
    
    public function getBookingEvents(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        
        $bookings = HotelBooking::whereHas('hotel', function($q) {
                $q->where('manager_id', $this->user->id);
            })
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('check_in', [$start, $end])
                  ->orWhereBetween('check_out', [$start, $end])
                  ->orWhere(function($q) use ($start, $end) {
                      $q->where('check_in', '<', $start)
                        ->where('check_out', '>', $end);
                  });
            })
            ->with(['hotel', 'room'])
            ->get();
            
        $events = [];
        foreach ($bookings as $booking) {
            $events[] = [
                'id' => $booking->id,
                'title' => "{$booking->room->name} - {$booking->guest_name}",
                'start' => $booking->check_in->toIsoString(),
                'end' => $booking->check_out->toIsoString(),
                'url' => route('hotel-manager.bookings.show', $booking),
                'color' => $booking->status === 'confirmed' ? '#28a745' : 
                          ($booking->status === 'pending' ? '#ffc107' : '#dc3545'),
                'extendedProps' => [
                    'status' => $booking->status,
                    'room' => $booking->room->name,
                    'guest' => $booking->guest_name,
                    'hotel' => $booking->hotel->name
                ]
            ];
        }
        
        return response()->json($events);
    }
    
    public function reports()
    {
        // Statistiques de réservations par mois pour l'année en cours
        $yearlyBookings = HotelBooking::select(
                DB::raw('MONTH(check_in) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->whereHas('hotel', function($q) {
                $q->where('manager_id', $this->user->id);
            })
            ->whereYear('check_in', now()->year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');
            
        // Préparer les données pour le graphique
        $months = [];
        $bookingsData = [];
        $revenueData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::createFromDate(null, $i, 1)->locale('fr')->monthName;
            $months[] = $monthName;
            $bookingsData[] = $yearlyBookings->has($i) ? $yearlyBookings[$i]->total : 0;
            $revenueData[] = $yearlyBookings->has($i) ? (float)$yearlyBookings[$i]->revenue : 0;
        }
        
        // Top hôtels par réservations
        $topHotels = Hotel::where('manager_id', $this->user->id)
            ->withCount(['bookings' => function($query) {
                $query->whereYear('created_at', now()->year);
            }])
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();
            
        // Taux d'occupation mensuel
        $occupancy = [];
        $currentMonth = now()->startOfMonth();
        
        for ($i = 0; $i < 6; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            $nextMonth = $month->copy()->addMonth();
            
            $totalRooms = Room::whereHas('hotel', function($q) {
                    $q->where('manager_id', $this->user->id);
                })
                ->where('created_at', '<=', $month->endOfMonth())
                ->count();
                
            $bookedNights = HotelBooking::whereHas('hotel', function($q) {
                    $q->where('manager_id', $this->user->id);
                })
                ->where('check_in', '<', $nextMonth)
                ->where('check_out', '>', $month)
                ->whereIn('status', ['confirmed', 'completed'])
                ->select(DB::raw('SUM(DATEDIFF(
                    LEAST(check_out, "' . $nextMonth->format('Y-m-d') . '"), 
                    GREATEST(check_in, "' . $month->format('Y-m-d') . '")
                )) as nights'))
                ->first()->nights ?? 0;
                
            $availableNights = $totalRooms * $month->daysInMonth;
            $occupancyRate = $availableNights > 0 ? round(($bookedNights / $availableNights) * 100, 2) : 0;
            
            $occupancy[] = [
                'month' => $month->locale('fr')->monthName,
                'rate' => $occupancyRate,
                'booked_nights' => $bookedNights,
                'available_nights' => $availableNights
            ];
        }
        
        $occupancy = array_reverse($occupancy);
        
        return view('hotel-manager.reports.index', compact(
            'months', 
            'bookingsData', 
            'revenueData', 
            'topHotels',
            'occupancy'
        ));
    }
}
