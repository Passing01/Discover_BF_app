<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Map simple fields to User
        $user->first_name = $data['first_name'] ?? $user->first_name;
        $user->last_name  = $data['last_name']  ?? $user->last_name;
        $user->email      = $data['email']      ?? $user->email;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Avatar upload (public disk)
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        // Ensure profile exists and update preferences
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        if (isset($data['primary_language'])) {
            $profile->primary_language = $data['primary_language'];
        }
        $prefs = $profile->preferences ?? [];
        if (isset($data['currency'])) {
            $prefs['currency'] = $data['currency'];
        }
        if (array_key_exists('notify_email', $data)) {
            $prefs['notify_email'] = (bool)$data['notify_email'];
        }
        $profile->preferences = $prefs;
        $profile->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
