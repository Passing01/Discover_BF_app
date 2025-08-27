<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        
        // Vérifier si l'utilisateur est un administrateur
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // Gestion de l'onboarding pour les autres rôles
        if ($user->role !== 'tourist' && empty($user->role_onboarded_at)) {
            return redirect()->route('onboarding.start');
        }

        // Redirection en fonction du rôle
        switch ($user->role) {
            case 'tourist':
                return redirect()->intended(route('tourist.dashboard', absolute: false));
            case 'guide':
                return redirect()->intended(route('guide.dashboard', absolute: false));
            case 'event_organizer':
                return redirect()->intended(route('organizer.dashboard', absolute: false));
            case 'hotel_manager':
                return redirect()->intended(route('hotel.manager.dashboard', absolute: false));
            default:
                return redirect()->intended('/');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
