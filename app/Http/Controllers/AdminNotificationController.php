<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminNotificationController extends Controller
{
    public function index()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        return view('admin.notifications');
    }

    public function sendToRole(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $validated = $request->validate([
            'role' => ['required','in:tourist,guide,admin,event_organizer,driver,hotel_manager'],
            'title' => ['required','string','max:150'],
            'message' => ['required','string','max:1000'],
            'type' => ['nullable','string','max:50'],
        ]);

        $users = User::where('role', $validated['role'])->where('is_active', true)->get();
        foreach ($users as $u) {
            Notification::create([
                'user_id' => $u->id,
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'] ?? 'broadcast',
                'read' => false,
                'sent_at' => now(),
            ]);
        }
        return Redirect::back()->with('status', 'Notification envoyée au rôle: '.$validated['role']);
    }
}
