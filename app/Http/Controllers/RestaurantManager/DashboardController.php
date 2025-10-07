<?php

namespace App\Http\Controllers\RestaurantManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('owner_id', Auth::id())->get();
        $reservations = RestaurantReservation::whereIn('restaurant_id', $restaurants->pluck('id'))
            ->with('restaurant')
            ->latest()
            ->take(10)
            ->get();

        return view('restaurant_manager.dashboard', compact('restaurants', 'reservations'));
    }
}
