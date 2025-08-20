<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'event_organizer') abort(403);
        $user = Auth::user();
        $events = Event::where('organizer_id', $user->id)->orderByDesc('created_at')->paginate(10);
        $adsSidebar = Ad::activeFor('organizer_sidebar')->orderByDesc('weight')->get();
        $adsFeed = Ad::activeFor('organizer_feed')->orderByDesc('weight')->get();
        return view('organizer.dashboard', compact('events','adsSidebar','adsFeed'));
    }
}
