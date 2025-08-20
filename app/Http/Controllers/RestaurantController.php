<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->paginate(12);
        return view('food.restaurants.index', compact('restaurants'));
    }

    public function show(Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);
        // Only show available dishes publicly
        $restaurant->load(['dishes' => function ($q) {
            $q->where('is_available', true)->orderBy('name');
        }]);
        return view('food.restaurants.show', compact('restaurant'));
    }

    public function createReservation(Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);
        $restaurant->load(['dishes' => function ($q) {
            $q->where('is_available', true)->orderBy('name');
        }]);
        return view('food.restaurants.reserve', compact('restaurant'));
    }

    public function storeReservation(Request $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        $data = $request->validate([
            'reservation_at' => ['required', 'date', 'after:now'],
            'party_size' => ['required', 'integer', 'min:1', 'max:20'],
            'special_requests' => ['nullable', 'string', 'max:500'],
            'items' => ['nullable', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:50'],
        ]);

        // Build order items array: [{dish_id, qty}]
        $orderItems = [];
        if (!empty($data['items'])) {
            // Reduce to positive quantities only and existing dishes
            $dishIds = collect($data['items'])
                ->filter(fn($qty) => (int)$qty > 0)
                ->keys()
                ->all();
            if (!empty($dishIds)) {
                $validIds = $restaurant->dishes()
                    ->whereIn('id', $dishIds)
                    ->where('is_available', true)
                    ->pluck('id')
                    ->all();
                foreach ($validIds as $id) {
                    $qty = (int)($data['items'][$id] ?? 0);
                    if ($qty > 0) {
                        $orderItems[] = ['dish_id' => $id, 'qty' => $qty];
                    }
                }
            }
        }

        $reservation = RestaurantReservation::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
            'reservation_at' => $data['reservation_at'],
            'party_size' => $data['party_size'],
            'status' => 'requested',
            'special_requests' => $data['special_requests'] ?? null,
            'order_items' => !empty($orderItems) ? $orderItems : null,
        ]);

        return redirect()->route('food.restaurants.reservations.show', $reservation);
    }

    public function showReservation(RestaurantReservation $reservation)
    {
        if (!Auth::check() || $reservation->user_id !== Auth::id()) {
            abort(403);
        }
        $reservation->load('restaurant');
        $orderedItems = [];
        if (!empty($reservation->order_items)) {
            $map = collect($reservation->order_items);
            $ids = $map->pluck('dish_id')->unique()->values()->all();
            if (!empty($ids)) {
                $dishes = $reservation->restaurant->dishes()->whereIn('id', $ids)->get(['id','name','price','image_path']);
                $byId = $dishes->keyBy('id');
                foreach ($map as $row) {
                    $dish = $byId->get($row['dish_id'] ?? null);
                    if ($dish && ($row['qty'] ?? 0) > 0) {
                        $orderedItems[] = [
                            'name' => $dish->name,
                            'qty' => (int)$row['qty'],
                            'price' => $dish->price,
                            'image' => $dish->image_path,
                        ];
                    }
                }
            }
        }
        return view('food.restaurants.reservation_show', compact('reservation'))
            ->with('orderedItems', $orderedItems);
    }

    public function myReservations()
    {
        if (!Auth::check()) {
            abort(403);
        }
        $reservations = RestaurantReservation::with('restaurant')
            ->where('user_id', Auth::id())
            ->orderByDesc('reservation_at')
            ->paginate(10);
        return view('food.restaurants.reservations_index', compact('reservations'));
    }
}
