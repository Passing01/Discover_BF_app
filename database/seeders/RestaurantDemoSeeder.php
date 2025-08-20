<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Dish;
use App\Models\RestaurantReservation;

class RestaurantDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there is at least one restaurant owner
        $owner = User::where('role', 'restaurant')->first();
        if (!$owner) {
            $owner = User::first(); // fallback to first user
        }

        $samples = [
            [
                'name' => 'Le Faso Gourmet', 'city' => 'Ouagadougou', 'address' => 'Avenue Kwame Nkrumah',
                'latitude' => 12.3686, 'longitude' => -1.5275, 'map_url' => 'https://maps.google.com/?q=12.3686,-1.5275',
                'avg_price' => 8000, 'rating' => 4.6,
                'gallery' => [
                    'assets/img/working-1.jpg',
                    'assets/img/working-2.jpg',
                ],
                'video_urls' => [
                    'https://www.youtube.com/shorts/O8_jSnTzFYs',
                ],
                'dishes' => [
                    ['Riz gras au poulet', 'Plat traditionnel avec riz parfumé et poulet', 4500, 'Africain', ['assets/img/working-3.jpg'], []],
                    ['Poulet braisé', 'Poulet mariné et grillé au feu de bois', 5500, 'Grillades', ['assets/img/working-2.jpg'], []],
                    ['Attiéké poisson', 'Attiéké servi avec poisson frit', 6000, 'Africain', ['assets/img/working-1.jpg'], []],
                ],
            ],
            [
                'name' => 'Savannah Bistro', 'city' => 'Bobo-Dioulasso', 'address' => 'Quartier Dioulassoba',
                'latitude' => 11.1783, 'longitude' => -4.2979, 'map_url' => 'https://maps.google.com/?q=11.1783,-4.2979',
                'avg_price' => 7000, 'rating' => 4.3,
                'gallery' => [
                    'assets/img/working-3.jpg',
                ],
                'video_urls' => [
                    'https://www.youtube.com/shorts/O8_jSnTzFYs',
                ],
                'dishes' => [
                    ['Brochettes de bœuf', 'Servies avec alloco et sauce épicée', 5000, 'Grillades', ['assets/img/working-2.jpg'], []],
                    ['Salade fraîcheur', 'Légumes de saison, vinaigrette maison', 3500, 'Salades', ['assets/img/working-1.jpg'], []],
                    ['Tô sauce arachide', 'Spécialité locale', 4000, 'Africain', ['assets/img/working-3.jpg'], []],
                ],
            ],
            [
                'name' => 'Oasis Café', 'city' => 'Ouagadougou', 'address' => 'Zogona',
                'latitude' => 12.3733, 'longitude' => -1.5197, 'map_url' => 'https://maps.google.com/?q=12.3733,-1.5197',
                'avg_price' => 5000, 'rating' => 4.1,
                'gallery' => [
                    'assets/img/working-1.jpg',
                ],
                'video_urls' => [
                    'https://www.youtube.com/shorts/O8_jSnTzFYs',
                ],
                'dishes' => [
                    ['Burger maison', 'Bœuf, fromage, sauce secrète', 4500, 'Snacks', ['assets/img/working-2.jpg'], []],
                    ['Pizza marguerita', 'Tomate, mozzarella, basilic', 6000, 'Pizzas', ['assets/img/working-3.jpg'], []],
                    ['Jus bissap', 'Boisson locale', 1000, 'Boissons', ['assets/img/working-1.jpg'], []],
                ],
            ],
        ];

        foreach ($samples as $r) {
            $restaurant = Restaurant::create([
                'owner_id' => $owner?->id,
                'name' => $r['name'],
                'slug' => Str::slug($r['name']).'-'.Str::random(6),
                'address' => $r['address'],
                'city' => $r['city'],
                'latitude' => $r['latitude'] ?? null,
                'longitude' => $r['longitude'] ?? null,
                'map_url' => $r['map_url'] ?? null,
                'description' => 'Cuisine authentique et ambiance conviviale.',
                'avg_price' => $r['avg_price'],
                'rating' => $r['rating'],
                'is_active' => true,
                'gallery' => $r['gallery'] ?? [],
                'video_urls' => $r['video_urls'] ?? [],
            ]);

            foreach ($r['dishes'] as $d) {
                Dish::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $d[0],
                    'description' => $d[1],
                    'price' => $d[2],
                    'category' => $d[3],
                    'gallery' => $d[4] ?? [],
                    'video_urls' => $d[5] ?? [],
                    'is_available' => true,
                ]);
            }

            // Sample reservations for demo (attach to any existing tourist)
            $user = User::where('role', 'tourist')->first();
            if ($user) {
                RestaurantReservation::create([
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurant->id,
                    'reservation_at' => now()->addDays(2)->setTime(19, 30),
                    'party_size' => 2,
                    'status' => 'confirmed',
                ]);
            }
        }
    }
}
