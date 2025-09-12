<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SiteManagerSiteController extends Controller
{
    /**
     * Affiche la liste des sites gérés par le gestionnaire
     */
    public function index()
    {
        $sites = Site::where('manager_id', Auth::id())
            ->withCount('bookings')
            ->latest()
            ->paginate(10);
            
        return view('site-manager.sites.index', compact('sites'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau site
     */
    public function create()
    {
        return view('site-manager.sites.create');
    }

    /**
     * Enregistre un nouveau site
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'website' => 'nullable|url|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|min:' . $request->price_min,
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Gestion du téléchargement de la photo
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/sites');
            $photoUrl = Storage::url($path);
        }

        // Création du site
        $site = new Site([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'website' => $validated['website'] ?? null,
            'price_min' => $validated['price_min'],
            'price_max' => $validated['price_max'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'photo_url' => $photoUrl,
            'manager_id' => Auth::id(),
            'is_active' => $request->has('is_active'),
        ]);

        $site->save();

        return redirect()
            ->route('site-manager.sites.index')
            ->with('success', 'Le site a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un site
     */
    public function show(Site $site)
    {
        $this->authorize('view', $site);
        
        $recentBookings = $site->bookings()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();
            
        return view('site-manager.sites.show', compact('site', 'recentBookings'));
    }

    /**
     * Affiche le formulaire de modification d'un site
     */
    public function edit(Site $site)
    {
        $this->authorize('update', $site);
        return view('site-manager.sites.edit', compact('site'));
    }

    /**
     * Met à jour un site existant
     */
    public function update(Request $request, Site $site)
    {
        $this->authorize('update', $site);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'website' => 'nullable|url|max:255',
            'price_min' => 'required|numeric|min:0',
            'price_max' => 'required|numeric|min:' . $request->price_min,
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Mise à jour de la photo si fournie
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($site->photo_url) {
                $oldPhoto = str_replace('/storage', 'public', $site->photo_url);
                Storage::delete($oldPhoto);
            }
            
            $path = $request->file('photo')->store('public/sites');
            $validated['photo_url'] = Storage::url($path);
        }

        // Mise à jour du site
        $site->fill($validated);
        $site->is_active = $request->has('is_active');
        $site->save();

        return redirect()
            ->route('site-manager.sites.show', $site)
            ->with('success', 'Le site a été mis à jour avec succès.');
    }

    /**
     * Supprime un site
     */
    public function destroy(Site $site)
    {
        $this->authorize('delete', $site);
        
        // Supprimer la photo si elle existe
        if ($site->photo_url) {
            $photoPath = str_replace('/storage', 'public', $site->photo_url);
            Storage::delete($photoPath);
        }
        
        $site->delete();
        
        return redirect()
            ->route('site-manager.sites.index')
            ->with('success', 'Le site a été supprimé avec succès.');
    }
    
    /**
     * Met à jour le statut d'activité d'un site
     */
    public function toggleStatus(Site $site)
    {
        $this->authorize('update', $site);
        
        $site->is_active = !$site->is_active;
        $site->save();
        
        return response()->json([
            'success' => true,
            'is_active' => $site->is_active
        ]);
    }
}
