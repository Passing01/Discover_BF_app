<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\GuideContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTouristSiteController extends Controller
{
    /**
     * Lister les sites touristiques.
     */
    public function index()
    {
        $sites = Site::query()->orderBy('city')->orderBy('name')->paginate(12);
        return response()->json($sites);
    }

    /**
     * Afficher les détails d'un site touristique.
     */
    public function show(string $id)
    {
        $site = Site::findOrFail($id);
        return response()->json($site);
    }

    /**
     * Contacter un guide pour un site touristique.
     */
    public function contactGuide(Request $request, string $id)
    {
        $site = Site::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:40',
            'message' => 'required|string|max:2000',
        ]);

        $guide = $site->manager; // Assume manager is the guide for simplicity

        GuideContact::create([
            'site_id' => $site->id,
            'guide_id' => $guide?->id,
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Votre demande a été envoyée à un guide.']);
    }
}