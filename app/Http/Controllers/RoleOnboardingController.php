<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\OrganizerProfile;
use App\Models\Restaurant;
use Illuminate\Support\Str;

class RoleOnboardingController extends Controller
{
    public function start()
    {
        $user = Auth::user();
        if (!$user) {
            return Redirect::route('login');
        }
        // If already onboarded or tourist, go to dashboard
        if ($user->role === 'tourist' || !empty($user->role_onboarded_at)) {
            return Redirect::route('dashboard');
        }

        // Render dynamic form fields for the user's role
        return view('onboarding.role', [
            'role' => $user->role,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return Redirect::route('login');
        }
        if ($user->role === 'tourist') {
            return Redirect::route('dashboard');
        }

        // Minimal validation depending on role
        $rules = [];
        switch ($user->role) {
            case 'guide':
                $rules = [
                    'bio' => ['required', 'string', 'max:1000'],
                    'languages' => ['nullable', 'string', 'max:255'],
                    'specialty' => ['nullable', 'string', 'max:255'],
                ];
                break;
            case 'event_organizer':
                $rules = [
                    'organization' => ['required', 'string', 'max:255'],
                    'website' => ['nullable', 'url'],
                    'brand_color' => ['nullable', 'string', 'max:20'],
                    'logo' => ['nullable', 'image', 'max:4096'],
                ];
                break;
            case 'driver':
                $rules = [
                    'license_number' => ['required', 'string', 'max:100'],
                    'vehicle' => ['required', 'string', 'max:100'],
                ];
                break;
            case 'hotel_manager':
                $rules = [
                    'hotel_name' => ['required', 'string', 'max:255'],
                    'address' => ['required', 'string', 'max:255'],
                ];
                break;
            case 'restaurant':
                $rules = [
                    'restaurant_name' => ['required', 'string', 'max:255'],
                    'address' => ['required', 'string', 'max:255'],
                    'city' => ['nullable', 'string', 'max:100'],
                    'phone' => ['nullable', 'string', 'max:50'],
                ];
                break;
            default:
                $rules = [];
        }

        $validated = $request->validate($rules);

        if ($user->role === 'event_organizer') {
            // Save organizer profile (logo optional)
            $payload = [
                'website' => $validated['website'] ?? null,
                'brand_color' => $validated['brand_color'] ?? null,
            ];
            if ($request->hasFile('logo')) {
                $payload['logo_path'] = $request->file('logo')->store('organizers/'.$user->id, 'public');
            }
            OrganizerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $payload
            );
        }

        if ($user->role === 'restaurant') {
            // Create or update a basic Restaurant profile for the owner
            Restaurant::updateOrCreate(
                ['owner_id' => $user->id],
                [
                    'name' => $validated['restaurant_name'] ?? 'Mon Restaurant',
                    'slug' => Str::slug(($validated['restaurant_name'] ?? 'restaurant')).'-'.Str::random(6),
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'is_active' => true,
                    'description' => 'Établissement ajouté via l\'onboarding.',
                ]
            );
        }

        // Persist into generic profile preferences as well
        $profile = $user->profile()->firstOrCreate([]);
        $prefs = $profile->preferences ?? [];
        $prefs['role_meta'][$user->role] = $validated;
        $profile->preferences = $prefs;
        $profile->save();

        $user->role_onboarded_at = now();
        $user->save();

        return Redirect::route('dashboard')->with('status', 'Onboarding terminé.');
    }
}
