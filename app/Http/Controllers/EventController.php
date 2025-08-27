<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Afficher la liste des événements
     */
    public function index()
    {
        $events = Event::with(['category', 'featuredImage'])
            ->where('organizer_id', Auth::id())
            ->latest()
            ->paginate(10);
            
        return view('site.events.index', compact('events'));
    }
    
    /**
     * Afficher le formulaire de création d'un événement
     */
    public function create()
    {
        $categories = EventCategory::active()->parentCategories()->with('children')->get();
        return view('site.events.create', compact('categories'));
    }
    
    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:event_categories,id',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_online' => 'boolean',
            'online_meeting_url' => 'nullable|required_if:is_online,true|url',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled'])],
            'featured_image' => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',
        ]);
        
        // Créer l'événement
        $event = Auth::user()->organizedEvents()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'postal_code' => $validated['postal_code'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_online' => $validated['is_online'] ?? false,
            'online_meeting_url' => $validated['online_meeting_url'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'price' => $validated['price'] ?? 0,
            'status' => $validated['status'],
        ]);
        
        // Gérer l'image à la une
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events/' . $event->id, 'public');
            
            $media = $event->media()->create([
                'file_name' => $request->file('featured_image')->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $request->file('featured_image')->getClientMimeType(),
                'size' => $request->file('featured_image')->getSize(),
                'type' => 'image',
                'is_featured' => true,
                'order' => 1
            ]);
            
            $event->update(['featured_image_id' => $media->id]);
        }
        
        // Gérer la galerie d'images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                $path = $file->store('events/' . $event->id, 'public');
                
                $event->media()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'type' => 'image',
                    'is_featured' => false,
                    'order' => $index + 2
                ]);
            }
        }
        
        return redirect()
            ->route('organizer.events.show', $event)
            ->with('success', 'Événement créé avec succès!');
    }
    
    /**
     * Afficher un événement
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);
        
        $event->load(['category', 'media', 'registrations']);
        
        return view('site.events.show', compact('event'));
    }
    
    /**
     * Afficher le formulaire d'édition d'un événement
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        
        $categories = EventCategory::active()->parentCategories()->with('children')->get();
        $event->load('media');
        
        return view('site.events.edit', compact('event', 'categories'));
    }
    
    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:event_categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_online' => 'boolean',
            'online_meeting_url' => 'nullable|required_if:is_online,true|url',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled'])],
            'featured_image' => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',
            'deleted_media' => 'nullable|array',
            'deleted_media.*' => 'exists:media,id',
        ]);
        
        // Mettre à jour l'événement
        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'postal_code' => $validated['postal_code'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_online' => $validated['is_online'] ?? false,
            'online_meeting_url' => $validated['online_meeting_url'] ?? null,
            'capacity' => $validated['capacity'] ?? null,
            'price' => $validated['price'] ?? 0,
            'status' => $validated['status'],
        ]);
        
        // Gérer l'image à la une
        if ($request->hasFile('featured_image')) {
            // Supprimer l'ancienne image à la une si elle existe
            if ($event->featuredImage) {
                Storage::disk('public')->delete($event->featuredImage->file_path);
                $event->featuredImage->delete();
            }
            
            $path = $request->file('featured_image')->store('events/' . $event->id, 'public');
            
            $media = $event->media()->create([
                'file_name' => $request->file('featured_image')->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $request->file('featured_image')->getClientMimeType(),
                'size' => $request->file('featured_image')->getSize(),
                'type' => 'image',
                'is_featured' => true,
                'order' => 1
            ]);
            
            $event->update(['featured_image_id' => $media->id]);
        }
        
        // Gérer la galerie d'images
        if ($request->hasFile('gallery')) {
            $lastOrder = $event->media()->max('order') ?? 1;
            
            foreach ($request->file('gallery') as $index => $file) {
                $path = $file->store('events/' . $event->id, 'public');
                
                $event->media()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'type' => 'image',
                    'is_featured' => false,
                    'order' => $lastOrder + $index + 1
                ]);
            }
        }
        
        // Supprimer les médias marqués pour suppression
        if (!empty($validated['deleted_media'])) {
            $medias = $event->media()->whereIn('id', $validated['deleted_media'])->get();
            
            foreach ($medias as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
                
                // Si c'était l'image à la une, la retirer
                if ($event->featured_image_id === $media->id) {
                    $event->update(['featured_image_id' => null]);
                }
            }
        }
        
        return redirect()
            ->route('organizer.events.show', $event)
            ->with('success', 'Événement mis à jour avec succès!');
    }
    
    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        // Supprimer les médias
        foreach ($event->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }
        
        // Supprimer les inscriptions
        $event->registrations()->delete();
        
        // Supprimer l'événement
        $event->delete();
        
        return redirect()
            ->route('organizer.events.index')
            ->with('success', 'Événement supprimé avec succès!');
    }
    
    /**
     * Afficher la page de gestion des inscriptions
     */
    public function registrations(Event $event)
    {
        $this->authorize('viewAnyRegistration', $event);
        
        $registrations = $event->registrations()
            ->with(['user'])
            ->latest()
            ->paginate(15);
            
        $stats = [
            'total' => $event->registrations()->count(),
            'confirmed' => $event->registrations()->where('status', 'confirmed')->count(),
            'pending' => $event->registrations()->where('status', 'pending')->count(),
            'cancelled' => $event->registrations()->where('status', 'cancelled')->count(),
        ];
            
        return view('site.events.registrations', [
            'event' => $event,
            'registrations' => $registrations,
            'stats' => $stats,
        ]);
    }
}
