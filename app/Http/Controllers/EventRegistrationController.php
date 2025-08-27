<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EventRegistrationsExport;

class EventRegistrationController extends Controller
{
    /**
     * Afficher la liste des inscriptions à un événement
     */
    public function index(Event $event)
    {
        $this->authorize('viewAnyRegistration', $event);
        
        $registrations = $event->registrations()
            ->with(['user'])
            ->latest()
            ->paginate(15);
            
        return view('site.events.registrations', [
            'event' => $event,
            'registrations' => $registrations,
        ]);
    }
    
    /**
     * Afficher les détails d'une inscription
     */
    public function show(EventRegistration $registration)
    {
        $this->authorize('view', $registration);
        
        return response()->json([
            'success' => true,
            'html' => view('site.events.partials.registration-details', [
                'registration' => $registration->load('user', 'event')
            ])->render()
        ]);
    }
    
    /**
     * Mettre à jour le statut d'une inscription
     */
    public function updateStatus(EventRegistration $registration, Request $request)
    {
        $this->authorize('update', $registration);
        
        $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled']
        ]);
        
        $registration->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'status_label' => $registration->status_label,
            'status_color' => $registration->status_color,
        ]);
    }
    
    /**
     * Exporter les inscriptions au format Excel
     */
    public function export(Event $event)
    {
        $this->authorize('exportRegistrations', $event);
        
        $filename = 'inscriptions-' . Str::slug($event->title) . '-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new EventRegistrationsExport($event), $filename);
    }
}
