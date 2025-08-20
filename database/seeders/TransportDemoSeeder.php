<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Taxi;
use App\Models\Bus;
use App\Models\BusTrip;
use Carbon\Carbon;

class TransportDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one active user exists (driver and demo traveler can be the same for seeding)
        $user = User::query()->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Seed a couple of taxis
        if (Taxi::count() === 0) {
            Taxi::create([
                'driver_id' => $user->id,
                'license_plate' => 'BF-1234-AB',
                'model' => 'Toyota Corolla',
                'color' => 'Jaune',
                'available' => true,
                'price_per_km' => 350.00,
            ]);

            Taxi::create([
                'driver_id' => $user->id,
                'license_plate' => 'BF-5678-CD',
                'model' => 'Hyundai i10',
                'color' => 'Jaune',
                'available' => true,
                'price_per_km' => 300.00,
            ]);
        }

        // Seed buses and trips
        if (Bus::count() === 0) {
            $bus = Bus::create([
                'name' => 'Discover Express',
                'license_plate' => 'BUS-1001',
                'capacity' => 50,
                'active' => true,
            ]);

            $now = Carbon::now();

            BusTrip::create([
                'bus_id' => $bus->id,
                'origin' => 'Ouagadougou',
                'destination' => 'Bobo-Dioulasso',
                'departure_time' => $now->copy()->addDay()->setTime(8, 0),
                'arrival_time' => $now->copy()->addDay()->setTime(12, 30),
                'price' => 7500.00,
                'seats_total' => 50,
                'seats_available' => 50,
            ]);

            BusTrip::create([
                'bus_id' => $bus->id,
                'origin' => 'Ouagadougou',
                'destination' => 'Koudougou',
                'departure_time' => $now->copy()->addDay()->setTime(15, 0),
                'arrival_time' => $now->copy()->addDay()->setTime(17, 0),
                'price' => 4500.00,
                'seats_total' => 50,
                'seats_available' => 50,
            ]);
        }
    }
}
