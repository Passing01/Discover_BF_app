<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Dish;
use App\Models\Event;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminModerationController extends Controller
{
    public function index()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $restaurants = Restaurant::orderByDesc('created_at')->limit(20)->get();
        $dishes = Dish::with('restaurant')->orderByDesc('created_at')->limit(20)->get();
        $events = Event::orderByDesc('created_at')->limit(20)->get();
        $hotels = Hotel::orderByDesc('created_at')->limit(20)->get();
        return view('admin.moderation', compact('restaurants','dishes','events','hotels'));
    }

    public function toggleRestaurant(Restaurant $restaurant)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $restaurant->is_active = !$restaurant->is_active;
        $restaurant->save();
        return Redirect::back()->with('status', 'Restaurant mis à jour.');
    }

    public function toggleDish(Dish $dish)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) abort(403);
        $dish->is_available = !$dish->is_available;
        $dish->save();
        return Redirect::back()->with('status', 'Plat mis à jour.');
    }
}
