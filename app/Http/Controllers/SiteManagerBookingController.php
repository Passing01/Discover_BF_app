<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingStatusUpdated;

class SiteManagerBookingController extends Controller
{
    /**
     * Affiche la liste des réservations pour les sites du gestionnaire
     */
    public function index()
    {
        $bookings = SiteBooking::whereHas('site', function($query) {
                $query->where('manager_id', Auth::id());
            })
            ->with(['site', 'user'])
            ->latest()
            ->filter(request(['status', 'search']))
            ->paginate(15);
            
        $statuses = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'cancelled' => 'Annulée', 'completed' => 'Terminée'];
        
        return view('site-manager.bookings.index', compact('bookings', 'statuses'));
    }

    /**
     * Affiche les détails d'une réservation
     */
    public function show(SiteBooking $booking)
    {
        $this->authorize('view', $booking);
        
        return view('site-manager.bookings.show', compact('booking'));
    }

    /**
     * Met à jour le statut d'une réservation
     */
    public function updateStatus(Request $request, SiteBooking $booking)
    {
        $this->authorize('update', $booking);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $oldStatus = $booking->status;
        $booking->status = $validated['status'];
        
        if (!empty($validated['notes'])) {
            $booking->admin_notes = $validated['notes'];
        }
        
        $booking->save();
        
        // Envoyer un email de notification si le statut a changé
        if ($oldStatus !== $booking->status) {
            try {
                Mail::to($booking->user->email)->send(
                    new BookingStatusUpdated($booking, $oldStatus)
                );
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas interrompre le flux
                \Log::error('Erreur lors de l\'envoi de l\'email de mise à jour de réservation: ' . $e->getMessage());
            }
        }
        
        return back()->with('success', 'Le statut de la réservation a été mis à jour avec succès.');
    }

    /**
     * Affiche le calendrier des réservations
     */
    public function calendar()
    {
        $sites = Site::where('manager_id', Auth::id())
            ->where('is_active', true)
            ->pluck('name', 'id');
            
        return view('site-manager.bookings.calendar', compact('sites'));
    }
    
    /**
     * Récupère les événements pour le calendrier
     */
    public function calendarEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'site_id' => 'nullable|exists:sites,id,manager_id,' . Auth::id(),
        ]);
        
        $query = SiteBooking::whereHas('site', function($query) {
                $query->where('manager_id', Auth::id());
            })
            ->whereBetween('visit_date', [$request->start, $request->end])
            ->with(['site', 'user']);
            
        if ($request->has('site_id') && $request->site_id) {
            $query->where('site_id', $request->site_id);
        }
        
        $bookings = $query->get();
        
        $events = [];
        
        foreach ($bookings as $booking) {
            $statusColors = [
                'pending' => '#f39c12',    // Orange
                'confirmed' => '#00a65a',  // Green
                'cancelled' => '#dd4b39',  // Red
                'completed' => '#3c8dbc',  // Blue
            ];
            
            $events[] = [
                'title' => $booking->site->name . ' - ' . $booking->visitors_count . ' pers.',
                'start' => $booking->visit_date->format('Y-m-d'),
                'end' => $booking->visit_date->format('Y-m-d'),
                'url' => route('site-manager.bookings.show', $booking),
                'backgroundColor' => $statusColors[$booking->status] ?? '#777',
                'borderColor' => $statusColors[$booking->status] ?? '#777',
                'extendedProps' => [
                    'visitors' => $booking->visitors_count,
                    'status' => $booking->status,
                    'status_label' => ucfirst($booking->status),
                    'price' => number_format($booking->total_amount, 0, ',', ' ') . ' FCFA',
                ]
            ];
        }
        
        return response()->json($events);
    }
    
    /**
     * Exporte les réservations au format CSV
     */
    public function export()
    {
        $bookings = SiteBooking::whereHas('site', function($query) {
                $query->where('manager_id', Auth::id());
            })
            ->with(['site', 'user'])
            ->latest()
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=reservations-' . date('Y-m-d') . '.csv',
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID', 'Site', 'Visiteur', 'Date de visite', 'Nombre de visiteurs',
                'Montant', 'Statut', 'Date de réservation'
            ]);
            
            // Données
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->site->name,
                    $booking->user->full_name,
                    $booking->visit_date->format('d/m/Y'),
                    $booking->visitors_count,
                    number_format($booking->total_amount, 0, ',', ' ') . ' FCFA',
                    ucfirst($booking->status),
                    $booking->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
