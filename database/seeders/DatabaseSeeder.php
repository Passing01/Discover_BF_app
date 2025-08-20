<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Register custom seeders
        $this->call([
            NonTouristUsersSeeder::class,
            AmenitiesAndRulesSeeder::class,
            TransportDemoSeeder::class,
            AirTravelDemoSeeder::class,
            SitesEventsSeeder::class,
            HotelsRoomsSeeder::class,
            EventsTicketsSeeder::class,
            TouristSitesSeeder::class,
            RestaurantDemoSeeder::class,
            AdsDemoSeeder::class,
        ]);
    }
}
