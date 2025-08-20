<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('sent_at')
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, Notification $notification)
    {
        $user = $request->user();
        abort_unless($notification->user_id === $user->id, 403);
        $notification->read = true;
        $notification->save();
        return Redirect::back()->with('status', 'Notification marquée comme lue.');
    }
}
