<?php

namespace App\Http\Controllers;

use App\Models\OrganizerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizerProfileController extends Controller
{
    protected function ensureRole(): void
    {
        if (Auth::user()?->role !== 'event_organizer' && !Auth::user()?->isAdmin()) {
            abort(403);
        }
    }

    public function editLogo()
    {
        $this->ensureRole();
        $user = Auth::user();
        $profile = OrganizerProfile::firstOrCreate(['user_id' => $user->id]);
        return view('organizer.profile.logo', compact('profile'));
    }

    public function updateLogo(Request $request)
    {
        $this->ensureRole();
        $user = Auth::user();
        $data = $request->validate([
            'logo' => ['required','image','max:4096'],
        ]);

        $profile = OrganizerProfile::firstOrCreate(['user_id' => $user->id]);

        // Remove old logo if exists
        if (!empty($profile->logo_path) && Storage::disk('public')->exists($profile->logo_path)) {
            Storage::disk('public')->delete($profile->logo_path);
        }

        $path = $request->file('logo')->store('organizers/'.$user->id, 'public');
        $profile->logo_path = $path;
        $profile->save();

        return redirect()->route('organizer.profile.logo.edit')->with('status', 'Logo mis à jour.');
    }
}
