<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class HotelManagerDashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'hotel_manager') abort(403);
        $user = Auth::user();
        $hotels = Hotel::where('manager_id', $user->id)->withCount('rooms')->orderByDesc('created_at')->paginate(10);
        $adsSidebar = Ad::activeFor('hotel_sidebar')->orderByDesc('weight')->get();
        $adsFeed = Ad::activeFor('hotel_feed')->orderByDesc('weight')->get();
        return view('hotel.dashboard', compact('hotels','adsSidebar','adsFeed'));
    }
}
