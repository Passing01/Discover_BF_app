<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Flight;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AirTravelDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure demo user exists
        $user = User::query()->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Burkina Faso airports
        $ouaga = Airport::firstOrCreate(
            ['iata_code' => 'OUA'],
            [
                'name' => 'Aéroport International de Ouagadougou',
                'city' => 'Ouagadougou',
                'country' => 'Burkina Faso',
                'latitude' => 12.3532,
                'longitude' => -1.5124,
                'photo_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1200&auto=format&fit=crop',
            ]
        );

        $bobo = Airport::firstOrCreate(
            ['iata_code' => 'BOY'],
            [
                'name' => 'Aéroport de Bobo-Dioulasso',
                'city' => 'Bobo-Dioulasso',
                'country' => 'Burkina Faso',
                'latitude' => 11.1606,
                'longitude' => -4.3308,
                'photo_url' => 'https://images.unsplash.com/photo-1509718443690-d8e2fb3474b7?q=80&w=1200&auto=format&fit=crop',
            ]
        );

        // Regional origins
        $abidjan = Airport::firstOrCreate(
            ['iata_code' => 'ABJ'],
            [
                'name' => 'Aéroport International Félix-Houphouët-Boigny',
                'city' => "Abidjan",
                'country' => 'Côte d\'Ivoire',
                'latitude' => 5.2614,
                'longitude' => -3.9263,
                'photo_url' => 'https://images.unsplash.com/photo-1502920917128-1aa500764b8a?q=80&w=1200&auto=format&fit=crop',
            ]
        );

        $accra = Airport::firstOrCreate(
            ['iata_code' => 'ACC'],
            [
                'name' => 'Kotoka International Airport',
                'city' => 'Accra',
                'country' => 'Ghana',
                'latitude' => 5.6052,
                'longitude' => -0.1668,
                'photo_url' => 'https://images.unsplash.com/photo-1498867354260-4f216aefc1fc?q=80&w=1200&auto=format&fit=crop',
            ]
        );

        // Seed sample flights with destination in Burkina Faso only
        if (Flight::count() === 0) {
            $base = Carbon::now()->addDays(2)->setTime(10, 0);
            Flight::create([
                'airline' => 'Air BF',
                'flight_number' => 'BF101',
                'origin_airport_id' => $abidjan->id,
                'destination_airport_id' => $ouaga->id,
                'departure_time' => $base,
                'arrival_time' => (clone $base)->addHours(2),
                'base_price' => 85000,
                'seats_total' => 160,
                'seats_available' => 160,
            ]);

            $base2 = Carbon::now()->addDays(3)->setTime(14, 30);
            Flight::create([
                'airline' => 'Sahel Air',
                'flight_number' => 'SH204',
                'origin_airport_id' => $accra->id,
                'destination_airport_id' => $ouaga->id,
                'departure_time' => $base2,
                'arrival_time' => (clone $base2)->addHours(2)->addMinutes(15),
                'base_price' => 99000,
                'seats_total' => 150,
                'seats_available' => 150,
            ]);

            // Domestic example within Burkina Faso
            $base3 = Carbon::now()->addDays(4)->setTime(9, 15);
            Flight::create([
                'airline' => 'Air BF',
                'flight_number' => 'BF302',
                'origin_airport_id' => $ouaga->id,
                'destination_airport_id' => $bobo->id,
                'departure_time' => $base3,
                'arrival_time' => (clone $base3)->addMinutes(55),
                'base_price' => 45000,
                'seats_total' => 60,
                'seats_available' => 60,
            ]);
        }
    }
}
