<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\Event;
use App\Models\Site\EventCategory;
use App\Models\Site\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    protected $site;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:site_manager');
        $this->middleware(function ($request, $next) {
            $this->site = Auth::user()->site;
            if (!$this->site) {
                abort(403, 'Aucun site touristique associé à ce compte.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = $this->site->events()->with(['category', 'registrations']);
        
        // Filtrage par statut
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'draft');
        }
        
        // Filtrage par date
        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('start_date', $request->date);
        }
        
        // Recherche
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $events = $query->latest('start_date')->paginate(15);
        
        return view('site.events.index', [
            'events' => $events,
            'statuses' => [
                'draft' => 'Brouillon',
                'published' => 'Publié',
                'ongoing' => 'En cours',
                'completed' => 'Terminé',
                'cancelled' => 'Annulé',
                'postponed' => 'Reporté',
            ],
            'filters' => $request->only(['status', 'date', 'search']),
        ]);
    }

    public function create()
    {
        $categories = EventCategory::active()->get();
        
        return view('site.events.create', [
            'categories' => $categories,
            'countries' => \App\Helpers\Countries::getCountries(),
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);
        
        try {
            $event = $this->site->events()->create([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'description' => $validated['description'],
                'category_id' => $validated['category_id'] ?? null,
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_date' => $validated['end_date'] ?? $validated['start_date'],
                'end_time' => $validated['end_time'] ?? null,
                'timezone' => $validated['timezone'] ?? 'Africa/Ouagadougou',
                'location_name' => $validated['location_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'region' => $validated['region'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? 'BF',
                'meeting_url' => $validated['meeting_url'] ?? null,
                'meeting_instructions' => $validated['meeting_instructions'] ?? null,
                'max_participants' => $validated['max_participants'] ?? null,
                'registration_deadline' => $validated['registration_deadline'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
                'is_free' => $validated['is_free'] ?? true,
                'status' => $validated['status'] ?? 'draft',
                'metadata' => [
                    'collect_phone' => $validated['collect_phone'] ?? false,
                    'collect_company' => $validated['collect_company'] ?? false,
                    'collect_position' => $validated['collect_position'] ?? false,
                    'custom_questions' => $validated['custom_questions'] ?? [],
                    'confirmation_message' => $validated['confirmation_message'] ?? null,
                ],
            ]);
            
            // Gestion des tags
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $event->retag($tags);
            }
            
            // Gestion de l'image à la une
            if ($request->hasFile('featured_image')) {
                $event->addMediaFromRequest('featured_image')
                    ->toMediaCollection('featured');
            }
            
            return redirect()->route('site.events.show', $event)
                ->with('success', 'L\'événement a été créé avec succès.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'événement: ' . $e->getMessage());
        }
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        
        $event->load(['category', 'registrations']);
        
        return view('site.events.show', [
            'event' => $event,
            'upcomingEvents' => $this->site->events()
                ->where('id', '!=', $event->id)
                ->where('status', 'published')
                ->where('start_date', '>=', now())
                ->orderBy('start_date')
                ->limit(3)
                ->get(),
        ]);
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        
        $categories = EventCategory::active()->get();
        
        return view('site.events.edit', [
            'event' => $event->load('media'),
            'categories' => $categories,
            'countries' => \App\Helpers\Countries::getCountries(),
            'timezones' => \DateTimeZone::listIdentifiers(),
            'tags' => $event->tags->pluck('name')->implode(','),
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        
        $validated = $this->validateEvent($request, $event->id);
        
        try {
            $event->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'description' => $validated['description'],
                'category_id' => $validated['category_id'] ?? null,
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_date' => $validated['end_date'] ?? $validated['start_date'],
                'end_time' => $validated['end_time'] ?? null,
                'timezone' => $validated['timezone'] ?? 'Africa/Ouagadougou',
                'location_name' => $validated['location_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'region' => $validated['region'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? 'BF',
                'meeting_url' => $validated['meeting_url'] ?? null,
                'meeting_instructions' => $validated['meeting_instructions'] ?? null,
                'max_participants' => $validated['max_participants'] ?? null,
                'registration_deadline' => $validated['registration_deadline'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
                'is_free' => $validated['is_free'] ?? true,
                'status' => $validated['status'] ?? 'draft',
                'metadata' => [
                    'collect_phone' => $validated['collect_phone'] ?? false,
                    'collect_company' => $validated['collect_company'] ?? false,
                    'collect_position' => $validated['collect_position'] ?? false,
                    'custom_questions' => $validated['custom_questions'] ?? [],
                    'confirmation_message' => $validated['confirmation_message'] ?? null,
                ],
            ]);
            
            // Gestion des tags
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
                $event->retag($tags);
            } else {
                $event->detag();
            }
            
            // Gestion de l'image à la une
            if ($request->hasFile('featured_image')) {
                $event->clearMediaCollection('featured');
                $event->addMediaFromRequest('featured_image')
                    ->toMediaCollection('featured');
            }
            
            return redirect()->route('site.events.show', $event)
                ->with('success', 'L\'événement a été mis à jour avec succès.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'événement: ' . $e->getMessage());
        }
    }

    public function updateStatus(Event $event, Request $request)
    {
        $this->authorize('update', $event);
        
        $validated = $request->validate([
            'status' => ['required', 'in:published,ongoing,completed,cancelled,postponed'],
            'reason' => 'required_if:status,cancelled,postponed|string|max:1000',
            'new_date' => 'required_if:status,postponed|date|after:today',
            'notify_attendees' => 'boolean',
        ]);
        
        try {
            $updates = ['status' => $validated['status']];
            
            if ($validated['status'] === 'postponed') {
                $updates['postponed_from'] = $event->start_date;
                $updates['start_date'] = $validated['new_date'];
                $updates['postponed_reason'] = $validated['reason'];
            } elseif ($validated['status'] === 'cancelled') {
                $updates['cancellation_reason'] = $validated['reason'];
                $updates['cancelled_at'] = now();
            } elseif ($validated['status'] === 'completed') {
                $updates['completed_at'] = now();
            }
            
            $event->update($updates);
            
            // Envoyer des notifications si nécessaire
            if ($validated['notify_attendees'] ?? false) {
                $this->notifyAttendees($event, $validated['status'], $validated['reason'] ?? null);
            }
            
            return back()->with('success', 'Le statut de l\'événement a été mis à jour.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du statut: ' . $e->getMessage());
        }
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        try {
            // Supprimer les médias associés
            $event->clearMediaCollection('featured');
            
            // Supprimer les inscriptions
            $event->registrations()->delete();
            
            // Supprimer l'événement
            $event->delete();
            
            return redirect()->route('site.events.index')
                ->with('success', 'L\'événement a été supprimé avec succès.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression de l\'événement: ' . $e->getMessage());
        }
    }

    public function registrations(Event $event)
    {
        $this->authorize('viewRegistrations', $event);
        
        $registrations = $event->registrations()
            ->latest()
            ->paginate(20);
        
        return view('site.events.registrations', [
            'event' => $event,
            'registrations' => $registrations,
        ]);
    }

    public function exportRegistrations(Event $event)
    {
        $this->authorize('exportRegistrations', $event);
        
        $registrations = $event->registrations()->get();
        
        $headers = [
            'ID', 'Nom', 'Email', 'Téléphone', 'Entreprise', 'Poste', 
            'Date d\'inscription', 'Statut'
        ];
        
        $rows = [];
        foreach ($registrations as $registration) {
            $rows[] = [
                $registration->id,
                $registration->name,
                $registration->email,
                $registration->phone,
                $registration->company,
                $registration->position,
                $registration->created_at->format('d/m/Y H:i'),
                $this->getRegistrationStatusLabel($registration->status),
            ];
        }
        
        return (new \App\Exports\GenericExport($headers, $rows))
            ->download('inscriptions-evenement-' . $event->id . '-' . now()->format('Y-m-d') . '.xlsx');
    }

    protected function validateEvent(Request $request, $eventId = null)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:event_categories,id',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'timezone' => 'required|timezone',
            'location_name' => 'required_without:meeting_url|string|max:255',
            'address' => 'required_without:meeting_url|string|max:500',
            'city' => 'required_without:meeting_url|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required_without:meeting_url|string|size:2',
            'meeting_url' => 'nullable|url|max:500',
            'meeting_instructions' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date|after_or_equal:today|before_or_equal:start_date',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'status' => 'required|in:draft,published,ongoing,completed,cancelled,postponed',
            'featured_image' => 'nullable|image|max:5120', // 5MB max
            'tags' => 'nullable|string|max:255',
            'collect_phone' => 'boolean',
            'collect_company' => 'boolean',
            'collect_position' => 'boolean',
            'custom_questions' => 'array',
            'custom_questions.*.question' => 'required_with:custom_questions|string|max:255',
            'custom_questions.*.type' => 'required_with:custom_questions|in:text,textarea,select,checkbox,radio',
            'custom_questions.*.required' => 'boolean',
            'custom_questions.*.options' => 'required_if:custom_questions.*.type,select,checkbox,radio|array',
            'custom_questions.*.options.*' => 'string|max:100',
            'confirmation_message' => 'nullable|string|max:1000',
        ];
        
        if ($eventId) {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('events', 'slug')->ignore($eventId),
            ];
        } else {
            $rules['slug'] = 'nullable|string|max:255|unique:events,slug';
        }
        
        return $request->validate($rules);
    }
    
    protected function notifyAttendees(Event $event, $status, $reason = null)
    {
        // Implémentez la logique d'envoi de notifications aux participants
        // Utilisez des notifications Laravel ou un service d'email tiers
    }
    
    protected function getRegistrationStatusLabel($status)
    {
        $statuses = [
            'registered' => 'Inscrit',
            'confirmed' => 'Confirmé',
            'cancelled' => 'Annulé',
            'waiting' => 'Liste d\'attente',
            'attended' => 'A participé',
            'no_show' => 'Ne s\'est pas présenté',
        ];
        
        return $statuses[$status] ?? $status;
    }
}
