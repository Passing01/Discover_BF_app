<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantOwnerController extends Controller
{
    protected function myRestaurantOrFail(): Restaurant
    {
        $user = Auth::user();
        abort_unless($user, 403);
        $restaurant = Restaurant::where('owner_id', $user->id)->first();
        abort_if(!$restaurant, 404, __('Aucun restaurant associé à ce compte.'));
        return $restaurant;
    }

    public function editRestaurant()
    {
        $restaurant = $this->myRestaurantOrFail();
        return view('food.owner.restaurant_edit', compact('restaurant'));
    }

    public function updateRestaurant(Request $request)
    {
        $restaurant = $this->myRestaurantOrFail();

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:100'],
            'latitude' => ['nullable','numeric','between:-90,90'],
            'longitude' => ['nullable','numeric','between:-180,180'],
            'map_url' => ['nullable','url'],
            'description' => ['nullable','string'],
            'avg_price' => ['nullable','numeric','min:0'],
            'cover_image' => ['nullable','image','max:5120'],
            'gallery.*' => ['nullable','image','max:8192'],
            'video_urls' => ['nullable','string'], // one per line
        ]);

        $restaurant->fill($data);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('restaurants/'.$restaurant->id, 'public');
            $restaurant->cover_image = $path;
        }

        $gallery = $restaurant->gallery ?? [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file) {
                    $gallery[] = $file->store('restaurants/'.$restaurant->id.'/gallery', 'public');
                }
            }
        }
        $restaurant->gallery = array_values(array_unique($gallery));

        if (!empty($data['video_urls'])) {
            $lines = preg_split('/\r?\n/', trim($data['video_urls']));
            $urls = array_values(array_filter(array_map('trim', $lines)));
            $restaurant->video_urls = $urls;
        }

        $restaurant->save();

        return back()->with('status', __('Restaurant mis à jour.'));
    }

    public function dishesIndex()
    {
        $restaurant = $this->myRestaurantOrFail();
        $dishes = $restaurant->dishes()->latest()->get();
        return view('food.owner.dishes_index', compact('restaurant','dishes'));
    }

    public function dishesStore(Request $request)
    {
        $restaurant = $this->myRestaurantOrFail();
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'category' => ['nullable','string','max:100'],
            'image' => ['nullable','image','max:5120'],
            'gallery.*' => ['nullable','image','max:8192'],
            'video_urls' => ['nullable','string'],
        ]);

        $dish = new Dish();
        $dish->restaurant_id = $restaurant->id;
        $dish->name = $data['name'];
        $dish->description = $data['description'] ?? null;
        $dish->price = $data['price'];
        $dish->category = $data['category'] ?? null;
        $dish->is_available = true;

        if ($request->hasFile('image')) {
            $dish->image_path = $request->file('image')->store('dishes/cover', 'public');
        }

        $gallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file) {
                    $gallery[] = $file->store('dishes/gallery', 'public');
                }
            }
        }
        $dish->gallery = $gallery;

        if (!empty($data['video_urls'])) {
            $lines = preg_split('/\r?\n/', trim($data['video_urls']));
            $dish->video_urls = array_values(array_filter(array_map('trim', $lines)));
        }

        $dish->save();
        return back()->with('status', __('Plat ajouté.'));
    }

    public function dishesEdit(Dish $dish)
    {
        $restaurant = $this->myRestaurantOrFail();
        abort_if($dish->restaurant_id !== $restaurant->id, 403);
        return view('food.owner.dish_edit', compact('restaurant','dish'));
    }

    public function dishesUpdate(Request $request, Dish $dish)
    {
        $restaurant = $this->myRestaurantOrFail();
        abort_if($dish->restaurant_id !== $restaurant->id, 403);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'category' => ['nullable','string','max:100'],
            'is_available' => ['nullable','boolean'],
            'image' => ['nullable','image','max:5120'],
            'gallery.*' => ['nullable','image','max:8192'],
            'video_urls' => ['nullable','string'],
        ]);

        $dish->fill([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category' => $data['category'] ?? null,
            'is_available' => (bool)($data['is_available'] ?? $dish->is_available),
        ]);

        if ($request->hasFile('image')) {
            $dish->image_path = $request->file('image')->store('dishes/cover', 'public');
        }

        $gallery = $dish->gallery ?? [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file) {
                    $gallery[] = $file->store('dishes/gallery', 'public');
                }
            }
        }
        $dish->gallery = array_values(array_unique($gallery));

        if (!empty($data['video_urls'])) {
            $lines = preg_split('/\r?\n/', trim($data['video_urls']));
            $dish->video_urls = array_values(array_filter(array_map('trim', $lines)));
        }

        $dish->save();
        return redirect()->route('food.owner.dishes.index')->with('status', __('Plat mis à jour.'));
    }

    public function dishesDestroy(Dish $dish)
    {
        $restaurant = $this->myRestaurantOrFail();
        abort_if($dish->restaurant_id !== $restaurant->id, 403);
        $dish->delete();
        return back()->with('status', __('Plat supprimé.'));
    }
}
