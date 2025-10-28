<?php

namespace App\Http\Controllers\RestaurantManager;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DishController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        // $this->authorize('view', $restaurant);
        
        $dishes = $restaurant->dishes()->latest()->paginate(10);
        return view('restaurant_manager.dishes.index', compact('restaurant', 'dishes'));
    }

    public function create(Restaurant $restaurant)
    {
        // $this->authorize('update', $restaurant);
        return view('restaurant_manager.dishes.create', compact('restaurant'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        // $this->authorize('update', $restaurant);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'is_available' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $dishData = $request->only(['name', 'description', 'price', 'category']);
        $dishData['is_available'] = $request->has('is_available');
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('dishes', 'public');
            $dishData['image_path'] = $path;
        }
        
        $restaurant->dishes()->create($dishData);
        
        return redirect()->route('restaurant-manager.restaurants.dishes.index', $restaurant->id)
            ->with('success', 'Le plat a été ajouté avec succès.');
    }

    public function edit(Restaurant $restaurant, Dish $dish)
    {
        // $this->authorize('update', $restaurant);
        $this->checkDishOwnership($restaurant, $dish);
        
        return view('restaurant_manager.dishes.edit', compact('restaurant', 'dish'));
    }

    public function show(Restaurant $restaurant, Dish $dish)
    {
        // $this->authorize('view', $restaurant);
        $this->checkDishOwnership($restaurant, $dish);
        return view('restaurant_manager.dishes.show', compact('restaurant','dish'));
    }

    public function update(Request $request, Restaurant $restaurant, Dish $dish)
    {
        // $this->authorize('update', $restaurant);
        $this->checkDishOwnership($restaurant, $dish);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'is_available' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'boolean',
        ]);

        $dishData = $request->only(['name', 'description', 'price', 'category']);
        $dishData['is_available'] = $request->has('is_available');
        
        if ($request->has('remove_image')) {
            if ($dish->image_path) {
                Storage::disk('public')->delete($dish->image_path);
                $dishData['image_path'] = null;
            }
        } elseif ($request->hasFile('image')) {
            if ($dish->image_path) {
                Storage::disk('public')->delete($dish->image_path);
            }
            $path = $request->file('image')->store('dishes', 'public');
            $dishData['image_path'] = $path;
        }
        
        $dish->update($dishData);
        
        return redirect()->route('restaurant-manager.restaurants.dishes.index', $restaurant->id)
            ->with('success', 'Le plat a été mis à jour avec succès.');
    }

    public function destroy(Restaurant $restaurant, Dish $dish)
    {
        // $this->authorize('update', $restaurant);
        $this->checkDishOwnership($restaurant, $dish);
        
        if ($dish->image_path) {
            Storage::disk('public')->delete($dish->image_path);
        }
        
        $dish->delete();
        
        return redirect()->route('restaurant-manager.restaurants.dishes.index', $restaurant->id)
            ->with('success', 'Le plat a été supprimé avec succès.');
    }
    
    protected function checkDishOwnership(Restaurant $restaurant, Dish $dish)
    {
        if ($dish->restaurant_id !== $restaurant->id) {
            abort(404);
        }
    }
}
