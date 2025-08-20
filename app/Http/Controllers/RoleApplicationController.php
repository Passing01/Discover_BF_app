<?php

namespace App\Http\Controllers;

use App\Models\RoleApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleApplicationController extends Controller
{
    /**
     * Show the partner application form.
     */
    public function create(Request $request)
    {
        return view('partner.apply');
    }

    /**
     * Store the partner application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_role' => 'required|in:guide,event_organizer,driver,hotel_manager,restaurant',
            'motivation' => 'required|string|min:20',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'languages' => 'nullable|string|max:255',
            'locations' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        RoleApplication::create([
            'user_id' => $user->id,
            'requested_role' => $validated['requested_role'],
            'data' => [
                'motivation' => $validated['motivation'],
                'experience_years' => $validated['experience_years'] ?? null,
                'languages' => $validated['languages'] ?? null,
                'locations' => $validated['locations'] ?? null,
            ],
            'status' => 'pending',
        ]);

        return redirect()->route('partner.apply')->with('status', __('Votre demande a été envoyée. Un administrateur vous répondra prochainement.'));
    }
}
