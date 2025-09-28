<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomPhoto;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomPhotoController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(Request $request, $hotelId, Room $room)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_main' => 'sometimes|boolean'
        ]);

        $uploadedPhotos = [];
        
        foreach ($request->file('photos') as $photo) {
            $path = $this->imageService->uploadHotelImage(
                $photo, 
                $room->hotel_id, 
                $request->input('is_main', false)
            );
            
            $uploadedPhotos[] = $room->photos()->create([
                'path' => $path,
                'is_main' => $request->input('is_main', false),
                'uploaded_by' => auth()->id()
            ]);
        }

        return response()->json([
            'message' => 'Photos uploaded successfully',
            'photos' => $uploadedPhotos
        ], 201);
    }

    public function destroy($hotelId, Room $room, RoomPhoto $photo)
    {
        if ($photo->room_id !== $room->id) {
            abort(404);
        }

        // Ne pas supprimer la photo principale s'il n'y a qu'une seule photo
        if ($photo->is_main && $room->photos()->count() === 1) {
            return response()->json([
                'message' => 'Cannot delete the only photo. Please add another photo first.'
            ], 422);
        }

        $this->imageService->deleteImage($photo->path);
        $photo->delete();

        // Si on a supprimé la photo principale, on en définit une nouvelle
        if ($photo->is_main && $room->photos()->exists()) {
            $newMainPhoto = $room->photos()->first();
            $newMainPhoto->update(['is_main' => true]);
        }

        return response()->json([
            'message' => 'Photo deleted successfully'
        ]);
    }

    public function setAsMain($hotelId, Room $room, RoomPhoto $photo)
    {
        if ($photo->room_id !== $room->id) {
            abort(404);
        }

        // Désactiver l'ancienne photo principale
        $room->photos()->where('is_main', true)->update(['is_main' => false]);
        
        // Définir la nouvelle photo principale
        $photo->update(['is_main' => true]);

        return response()->json([
            'message' => 'Main photo updated successfully'
        ]);
    }
}
