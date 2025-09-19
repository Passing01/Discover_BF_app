<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantReservationRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantReservationResource;
use App\Http\Resources\DishResource;
use App\Models\Restaurant;
use App\Models\RestaurantReservation;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Liste des restaurants (public).
     */
    public function index()
    {
        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->orderByDesc('rating')
            ->paginate(12);

        return RestaurantResource::collection($restaurants);
    }

    /**
     * Détails d'un restaurant (public).
     */
    public function show(Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);
        // Charger uniquement les plats disponibles pour l'affichage
        $restaurant->load(['dishes' => function ($q) {
            $q->where('is_available', true)->orderBy('name');
        }]);

        return new RestaurantResource($restaurant);
    }

    /**
     * Lister les plats disponibles d'un restaurant (public).
     */
    public function dishes(Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);
        $dishes = $restaurant->dishes()
            ->where('is_available', true)
            ->orderBy('name')
            ->get();
        return DishResource::collection($dishes);
    }

    /**
     * Créer une réservation (protégée par Sanctum).
     */
    public function reserve(StoreRestaurantReservationRequest $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        $validated = $request->validated();

        // Construire l'array order_items: [{dish_id, qty}]
        $orderItems = [];
        $items = $validated['items'] ?? [];
        if (!empty($items)) {
            $dishIds = collect($items)->pluck('dish_id')->filter()->unique()->values()->all();
            if (!empty($dishIds)) {
                $validIds = $restaurant->dishes()
                    ->whereIn('id', $dishIds)
                    ->where('is_available', true)
                    ->pluck('id')
                    ->all();
                $validSet = array_flip($validIds);
                foreach ($items as $row) {
                    $dishId = $row['dish_id'] ?? null;
                    $qty = (int)($row['qty'] ?? 0);
                    if ($dishId && isset($validSet[$dishId]) && $qty > 0) {
                        $orderItems[] = ['dish_id' => $dishId, 'qty' => $qty];
                    }
                }
            }
        }

        $reservation = RestaurantReservation::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
            'reservation_at' => $validated['reservation_at'],
            'party_size' => $validated['party_size'],
            'status' => 'requested',
            'payment_status' => RestaurantReservation::PAYMENT_STATUS_PENDING,
            'special_requests' => $validated['special_requests'] ?? null,
            'order_items' => !empty($orderItems) ? $orderItems : null,
        ]);

        return (new RestaurantReservationResource($reservation))
            ->response()
            ->setStatusCode(201);
    }
}
