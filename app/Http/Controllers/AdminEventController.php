<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Facades\DB;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $date = $request->string('date')->toString();
        $location = $request->string('location')->toString();

        $query = Event::query()->latest();

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }
        if ($location !== '') {
            $query->where('location', 'like', "%{$location}%");
        }
        if ($date !== '') {
            // Match events whose range contains the date
            // Also include events with NULL end_date (single-day or open-ended)
            $query->whereDate('start_date', '<=', $date)
                  ->where(function($w) use ($date) {
                      $w->whereDate('end_date', '>=', $date)
                        ->orWhereNull('end_date');
                  });
        }

        $events = $query->paginate(10)->appends($request->query());
        return view('admin.events', compact('events'));
    }

    public function sendFestivalAlert(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        // Minimal demo: create a Notification tied to the event organizer (fallback to current admin)
        DB::transaction(function() use ($data, $request) {
            $event = Event::findOrFail($data['event_id']);
            $targetUserId = $event->organizer_id ?? $request->user()->id;

            NotificationModel::create([
                'user_id' => $targetUserId,
                'title'   => 'Alerte Festival',
                'message' => $data['message'],
                'type'    => 'festival_alert',
                'sent_at' => now(),
            ]);
        });

        return back()->with('status', 'Alerte envoyée (démo).');
    }
}
