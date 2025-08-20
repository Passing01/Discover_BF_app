<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    public function show(Dish $dish)
    {
        $dish->load('restaurant');
        // Hide dish page if restaurant inactive or dish unavailable
        abort_unless(optional($dish->restaurant)->is_active && $dish->is_available, 404);
        return view('food.dishes.show', compact('dish'));
    }

    public function orderCreate(Dish $dish)
    {
        $dish->load('restaurant');
        abort_unless(optional($dish->restaurant)->is_active && $dish->is_available, 404);
        return view('food.dishes.order', compact('dish'));
    }

    public function orderStore(Request $request, Dish $dish)
    {
        $dish->load('restaurant');
        abort_unless(optional($dish->restaurant)->is_active && $dish->is_available, 404);

        $data = $request->validate([
            'quantity' => ['required','integer','min:1','max:50'],
            'delivery_address' => ['required','string','max:500'],
            'delivery_lat' => ['nullable','numeric','between:-90,90'],
            'delivery_lng' => ['nullable','numeric','between:-180,180'],
            'delivery_time' => ['nullable','date','after:now'],
            'notes' => ['nullable','string','max:500'],
        ]);

        $total = (int)$data['quantity'] * (float)$dish->price;

        $order = DishOrder::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $dish->restaurant_id,
            'dish_id' => $dish->id,
            'quantity' => (int)$data['quantity'],
            'delivery_address' => $data['delivery_address'],
            'delivery_lat' => $data['delivery_lat'] ?? null,
            'delivery_lng' => $data['delivery_lng'] ?? null,
            'delivery_time' => $data['delivery_time'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
            'total_price' => $total,
        ]);

        return redirect()->route('food.dishes.orders.show', $order);
    }

    public function orderShow(DishOrder $order)
    {
        if (!Auth::check() || $order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['dish','restaurant']);
        return view('food.dishes.order_show', compact('order'));
    }
}
