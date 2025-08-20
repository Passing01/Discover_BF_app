<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminAdController extends Controller
{
    public function index()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $ads = Ad::orderByDesc('enabled')->orderBy('placement')->orderByDesc('weight')->paginate(20);
        return view('admin.ads', compact('ads'));
    }

    public function store(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $validated = $request->validate([
            'placement' => ['required','string','max:100'],
            'title' => ['required','string','max:150'],
            'image_path' => ['nullable','string','max:255'],
            'target_url' => ['nullable','string','max:255'],
            'cta_text' => ['nullable','string','max:50'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'enabled' => ['nullable','boolean'],
            'weight' => ['nullable','integer','min:0','max:1000'],
        ]);
        $validated['enabled'] = (bool)($validated['enabled'] ?? true);
        $validated['weight'] = $validated['weight'] ?? 0;
        Ad::create($validated);
        return Redirect::back()->with('status', 'Publicité créée.');
    }

    public function toggle(Ad $ad)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $ad->enabled = !$ad->enabled;
        $ad->save();
        return Redirect::back()->with('status', 'Statut mis à jour.');
    }
}
