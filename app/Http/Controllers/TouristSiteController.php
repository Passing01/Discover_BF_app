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

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:40',
            'message' => 'required|string|max:2000',
        ]);

        $guide = User::where('role', 'guide')->orderBy('id')->first();

        GuideContact::create([
            'site_id' => $site->id,
            'guide_id' => $guide?->id,
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
        ]);

        return back()->with('status', 'Votre demande a été envoyée à un guide. Vous serez contacté(e) prochainement.');
    }
}
