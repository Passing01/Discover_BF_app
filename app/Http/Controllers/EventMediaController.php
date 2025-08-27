<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventMediaController extends Controller
{
    /**
     * Télécharger un média pour un événement
     */
    public function store(Event $event, Request $request)
    {
        $this->authorize('update', $event);
        
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10240',
            'type' => 'required|in:image,document',
            'is_featured' => 'sometimes|boolean'
        ]);
        
        $file = $request->file('file');
        $path = $file->store('events/' . $event->id, 'public');
        
        $media = $event->media()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'type' => $request->type,
            'is_featured' => $request->boolean('is_featured', false),
            'order' => $event->media()->count() + 1
        ]);
        
        // Si c'est l'image à la une, mettre à jour l'événement
        if ($request->boolean('is_featured', false)) {
            $event->update(['featured_image_id' => $media->id]);
        }
        
        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => Storage::url($path)
        ]);
    }
    
    /**
     * Supprimer un média
     */
    public function destroy(Media $media)
    {
        $this->authorize('delete', $media);
        
        // Supprimer le fichier physique
        Storage::disk('public')->delete($media->file_path);
        
        // Si c'est l'image à la une d'un événement, la retirer
        $media->event->update(['featured_image_id' => null]);
        
        // Supprimer l'entrée en base
        $media->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Média supprimé avec succès'
        ]);
    }
}
