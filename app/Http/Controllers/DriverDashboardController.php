<?php

namespace App\Http\Controllers;

use App\Models\Taxi;
use App\Models\Ride;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class DriverDashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'driver') abort(403);
        $user = Auth::user();
        $taxi = Taxi::where('driver_id', $user->id)->first();
        $rides = Ride::where('user_id', $user->id)->orderByDesc('created_at')->paginate(10);
        $adsSidebar = Ad::activeFor('driver_sidebar')->orderByDesc('weight')->get();
        $adsFeed = Ad::activeFor('driver_feed')->orderByDesc('weight')->get();
        return view('driver.dashboard', compact('taxi','rides','adsSidebar','adsFeed'));
    }
}
