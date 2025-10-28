<?php

namespace App\Http\Controllers\RestaurantManager;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function index()
    {
        // Récupérer tous les restaurants actifs avec leurs plats (avec pagination)
        $restaurants = Restaurant::with('dishes')
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN owner_id = ? THEN 0 ELSE 1 END', [Auth::id()])
            ->orderBy('name')
            ->paginate(10); // 10 restaurants par page
            
        return view('restaurant_manager.restaurants.index', compact('restaurants'));
    }

    public function create()
    {
        // Utiliser la vue unique du formulaire pour éviter la duplication
        return view('restaurant_manager.restaurants.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'required|string',
            'avg_price' => 'required|numeric|min:0',
            'cover_image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'video_urls.*' => 'nullable|url|max:255',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // Traitement de l'image de couverture
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants/cover', 'public');
        }

        // Création du restaurant
        $restaurant = Restaurant::create($validated);

        // Traitement de la galerie d'images
        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('restaurants/gallery', 'public');
                $gallery[] = $path;
            }
            $restaurant->gallery = $gallery;
        }

        // Traitement des URLs de vidéos
        if ($request->has('video_urls')) {
            $videoUrls = array_filter($request->input('video_urls', []));
            if (!empty($videoUrls)) {
                $restaurant->video_urls = $videoUrls;
            }
        }

        $restaurant->save();

        return redirect()->route('restaurant-manager.restaurants.index')
            ->with('success', 'Restaurant créé avec succès');
    }

    public function edit(Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);
        // Utiliser la vue unique du formulaire pour éviter la duplication
        return view('restaurant_manager.restaurants.form', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'required|string',
            'avg_price' => 'required|numeric|min:0',
            'cover_image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'removed_gallery_images' => 'nullable|array',
            'removed_gallery_images.*' => 'string',
            'video_urls' => 'nullable|array',
            'video_urls.*' => 'nullable|url|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // Traitement de l'image de couverture
        if ($request->hasFile('cover_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($restaurant->cover_image) {
                Storage::disk('public')->delete($restaurant->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants/cover', 'public');
        }

        // Mise à jour des champs de base
        $restaurant->update($validated);

        // Traitement des images de la galerie
        $gallery = $restaurant->gallery ?? [];
        
        // Suppression des images supprimées
        if ($request->has('removed_gallery_images')) {
            foreach ($request->input('removed_gallery_images', []) as $imageToRemove) {
                // Supprimer le fichier du stockage
                if (Storage::disk('public')->exists($imageToRemove)) {
                    Storage::disk('public')->delete($imageToRemove);
                }
                // Retirer de la galerie
                $gallery = array_filter($gallery, function($image) use ($imageToRemove) {
                    return $image !== $imageToRemove;
                });
            }
        }

        // Ajout des nouvelles images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $image->store('restaurants/gallery', 'public');
                $gallery[] = $path;
            }
        }

        // Mise à jour de la galerie
        $restaurant->gallery = array_values($gallery); // Réindexer le tableau

        // Traitement des URLs de vidéos
        if ($request->has('video_urls')) {
            $videoUrls = array_filter($request->input('video_urls', []));
            $restaurant->video_urls = $videoUrls;
        }

        $restaurant->save();

        return redirect()->route('restaurant-manager.restaurants.index')
            ->with('success', 'Restaurant mis à jour avec succès');
    }

    public function destroy(Restaurant $restaurant)
    {
        $this->authorize('delete', $restaurant);
        
        // Supprimer l'image de couverture
        if ($restaurant->cover_image) {
            Storage::disk('public')->delete($restaurant->cover_image);
        }
        
        // Supprimer les images de la galerie
        if (!empty($restaurant->gallery)) {
            foreach ($restaurant->gallery as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }
        
        $restaurant->delete();
        
        return redirect()->route('restaurant-manager.restaurants.index')
            ->with('success', 'Restaurant supprimé avec succès');
    }

    public function show(Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);
        return view('restaurant_manager.restaurants.show', compact('restaurant'));
    }
}
