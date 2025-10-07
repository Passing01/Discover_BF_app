<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\GuideContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TouristSiteController extends Controller
{
    public function index()
    {
        $sites = Site::query()->orderBy('city')->orderBy('name')->paginate(12);
        return view('tourist.sites.index', compact('sites'));
    }

    public function show(string $id)
    {
        $site = Site::findOrFail($id);
        // pick a guide; for MVP select the first guide user
        $guide = User::where('role', 'guide')->orderBy('id')->first();
        return view('tourist.sites.show', compact('site', 'guide'));
    }

    public function contactGuide(Request $request, string $id)
    {
        $site = Site::findOrFail($id);
        
        // Vérifier si l'utilisateur est un guide déjà affilié à ce site
        if (auth()->check() && auth()->user()->role === 'guide') {
            $isAffiliated = GuideContact::where('site_id', $site->id)
                ->where('guide_id', auth()->id())
                ->exists();
                
            if ($isAffiliated) {
                return back()->with('error', 'Vous êtes déjà affilié à ce site touristique.');
            }
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:40',
            'message' => 'required|string|max:2000',
        ]);

        $guide = User::where('role', 'guide')->orderBy('id')->first();

        // Vérifier que l'utilisateur n'est pas un guide essayant de s'envoyer un message à lui-même
        if (auth()->check() && auth()->user()->role === 'guide' && $guide && auth()->id() === $guide->id) {
            return back()->with('error', 'Vous ne pouvez pas vous envoyer de message à vous-même.');
        }

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

        return back()->with('status', 'Votre demande a été envoyée à un guide. Vous serez contacté(e) prochainement.');
    }
}
