<?php

namespace App\Http\Controllers;

use App\Models\RoleApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;

class AdminUserController extends Controller
{
    public function index()
    {
        // Optional: gate check
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $apps = RoleApplication::with('user')->latest()->paginate(15);
        return view('admin.role_apps', compact('apps'));
    }

    public function approve(string $id)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $app = RoleApplication::findOrFail($id);
        $user = User::findOrFail($app->user_id);

        // set user's role to requested_role (admin must use this endpoint responsibly)
        $user->role = $app->requested_role;
        $user->save();

        $app->status = 'approved';
        $app->reviewed_by = Auth::id();
        $app->reviewed_at = now();
        $app->save();

        return Redirect::route('admin.role_apps')->with('status', 'Demande approuvée.');
    }

    public function reject(string $id, Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $app = RoleApplication::findOrFail($id);
        $app->status = 'rejected';
        $app->reviewed_by = Auth::id();
        $app->reviewed_at = now();
        $app->save();

        return Redirect::route('admin.role_apps')->with('status', 'Demande rejetée.');
    }

    public function users(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $q = $request->string('q')->toString();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();

        $query = User::query()->orderBy('created_at', 'desc');

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }
        if ($role !== '') {
            $query->where('role', $role);
        }
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->paginate(20)->appends($request->query());
        return view('admin.users', compact('users'));
    }

    public function updateRole(string $id, Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $validated = $request->validate([
            'role' => ['required', 'in:tourist,guide,admin,event_organizer,driver,hotel_manager'],
        ]);
        $user = User::findOrFail($id);
        $user->role = $validated['role'];
        // Reset onboarding if role changed to a non-tourist role
        if ($user->role === 'tourist') {
            $user->role_onboarded_at = now();
        } else {
            $user->role_onboarded_at = null;
        }
        $user->save();
        return Redirect::route('admin.users')->with('status', 'Rôle mis à jour.');
    }

    public function resetOnboarding(string $id)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        $user->role_onboarded_at = null;
        $user->save();
        return Redirect::route('admin.users')->with('status', 'Onboarding réinitialisé.');
    }

    public function activate(string $id)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->save();
        return Redirect::route('admin.users')->with('status', 'Compte activé.');
    }

    public function deactivate(string $id)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        $user->is_active = false;
        $user->save();
        return Redirect::route('admin.users')->with('status', 'Compte désactivé.');
    }

    public function edit(string $id)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function update(string $id, Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'phone' => ['nullable','string','max:50'],
            'role' => ['required','in:tourist,guide,admin,event_organizer,driver,hotel_manager'],
            'is_active' => ['required','boolean'],
        ]);

        $user->fill($validated);
        $user->save();
        return Redirect::route('admin.users')->with('status', 'Utilisateur mis à jour.');
    }

    public function destroy(string $id)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        // Prevent self-delete for safety
        if ($user->id === Auth::id()) {
            return Redirect::back()->with('status', "Vous ne pouvez pas supprimer votre propre compte.");
        }
        $user->delete();
        return Redirect::route('admin.users')->with('status', 'Utilisateur supprimé.');
    }
}
