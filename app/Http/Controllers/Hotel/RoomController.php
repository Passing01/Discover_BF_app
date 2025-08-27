<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hotel_manager');
    }

    public function index()
    {
        $hotel = $this->getHotel();
        $rooms = $hotel->rooms()
            ->with(['type', 'bookings' => function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in']);
            }])
            ->latest()
            ->paginate(15);

        return view('hotel.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomTypes = RoomType::active()->get();
        $amenities = Amenity::forRooms()->get();
        
        return view('hotel.rooms.create', compact('roomTypes', 'amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'nullable|integer|min:0',
            'capacity' => 'required|integer|min:1|max:10',
            'price_per_night' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'boolean',
        ]);

        $hotel = $this->getHotel();
        
        try {
            $room = $hotel->rooms()->create([
                'room_number' => $validated['room_number'],
                'room_type_id' => $validated['room_type_id'],
                'floor' => $validated['floor'] ?? 0,
                'capacity' => $validated['capacity'],
                'price_per_night' => $validated['price_per_night'],
                'description' => $validated['description'] ?? null,
                'is_available' => $validated['is_available'] ?? true,
            ]);

            // Sync amenities
            if (!empty($validated['amenities'])) {
                $room->amenities()->sync($validated['amenities']);
            }

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store("hotels/{$hotel->id}/rooms/{$room->id}", 'public');
                    $room->photos()->create([
                        'path' => $path,
                        'is_primary' => $room->photos()->count() === 0,
                    ]);
                }
            }

            return redirect()->route('hotel.rooms.index')
                ->with('success', 'La chambre a été ajoutée avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la chambre.');
        }
    }

    public function edit(Room $room)
    {
        $this->authorize('update', $room);
        
        $room->load(['amenities', 'photos']);
        $roomTypes = RoomType::active()->get();
        $amenities = Amenity::forRooms()->get();
        
        return view('hotel.rooms.edit', compact('room', 'roomTypes', 'amenities'));
    }

    public function update(Request $request, Room $room)
    {
        $this->authorize('update', $room);
        
        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'nullable|integer|min:0',
            'capacity' => 'required|integer|min:1|max:10',
            'price_per_night' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_available' => 'boolean',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'exists:media,id',
        ]);

        try {
            $room->update([
                'room_number' => $validated['room_number'],
                'room_type_id' => $validated['room_type_id'],
                'floor' => $validated['floor'] ?? 0,
                'capacity' => $validated['capacity'],
                'price_per_night' => $validated['price_per_night'],
                'description' => $validated['description'] ?? null,
                'is_available' => $validated['is_available'] ?? true,
            ]);

            // Sync amenities
            $room->amenities()->sync($validated['amenities'] ?? []);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store("hotels/{$room->hotel_id}/rooms/{$room->id}", 'public');
                    $room->photos()->create([
                        'path' => $path,
                        'is_primary' => $room->photos()->count() === 0,
                    ]);
                }
            }

            // Handle photo deletions
            if (!empty($validated['delete_photos'])) {
                $photosToDelete = $room->photos()->whereIn('id', $validated['delete_photos'])->get();
                
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }

                // If primary photo was deleted, set a new one if available
                if ($room->photos()->count() > 0 && !$room->photos()->where('is_primary', true)->exists()) {
                    $room->photos()->first()->update(['is_primary' => true]);
                }
            }

            return redirect()->route('hotel.rooms.index')
                ->with('success', 'La chambre a été mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la chambre.');
        }
    }

    public function destroy(Room $room)
    {
        $this->authorize('delete', $room);
        
        try {
            // Delete associated photos from storage
            foreach ($room->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }
            
            $room->delete();
            
            return redirect()->route('hotel.rooms.index')
                ->with('success', 'La chambre a été supprimée avec succès.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la chambre.');
        }
    }

    public function updateStatus(Room $room, Request $request)
    {
        $this->authorize('update', $room);
        
        $validated = $request->validate([
            'status' => 'required|in:available,maintenance,cleaning',
            'reason' => 'required_if:status,maintenance|string|max:255',
        ]);
        
        $room->update([
            'status' => $validated['status'],
            'status_reason' => $validated['reason'] ?? null,
        ]);
        
        return back()->with('success', 'Le statut de la chambre a été mis à jour.');
    }
    
    protected function getHotel()
    {
        return Hotel::where('manager_id', Auth::id())->firstOrFail();
    }
}
