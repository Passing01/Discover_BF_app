<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelPhoto;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class HotelsRoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'hotel@discoverbf.test')->first();
        if (!$manager) {
            // Create the hotel manager user if missing
            $manager = User::create([
                'first_name' => 'Issa',
                'last_name' => 'Kaboré',
                'email' => 'hotel@discoverbf.test',
                'password' => bcrypt('password'),
                'role' => 'hotel_manager',
                'role_onboarded_at' => null,
                'email_verified_at' => now(),
            ]);
            $this->command?->info('Created hotel manager user (hotel@discoverbf.test / password).');
        }

        $cities = [
            'Ouagadougou','Bobo-Dioulasso','Koudougou','Banfora','Ouahigouya',
            'Kaya','Tenkodogo','Fada N’Gourma','Dori','Gaoua'
        ];

        $hotelNames = [
            'Hôtel Sahel','Résidence Faso','Auberge des Cascades','Palais du Soudan','Relais Gourmantché',
            'Émeraude Kadiogo','Horizon Larlé','Maison Gourcy','Oasis Yatenga','Savane Liptako'
        ];

        foreach (range(0,9) as $i) {
            $name = $hotelNames[$i] ?? ('Hôtel Burkina '.($i+1));
            $city = $cities[$i] ?? 'Ouagadougou';

            $hotel = Hotel::firstOrCreate(
                ['name' => $name, 'city' => $city],
                [
                    'id' => (string) Str::uuid(),
                    'address' => 'Quartier Central',
                    'country' => 'Burkina Faso',
                    'phone' => '+226 50 00 00 0'.($i+1),
                    'email' => 'contact'.($i+1).'@example.test',
                    'description' => 'Hébergement confortable au cœur du Burkina Faso avec accueil chaleureux et services modernes.',
                    'stars' => rand(2,5),
                    'photo' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=1200&auto=format&fit=crop',
                    'latitude' => 12.36 + ($i * 0.01),
                    'longitude' => -1.53 - ($i * 0.01),
                    'manager_id' => $manager->id,
                    'created_at' => now(), 'updated_at' => now(),
                ]
            );

            // At least one hotel gallery photo
            HotelPhoto::firstOrCreate([
                'hotel_id' => $hotel->id,
                'path' => 'https://images.unsplash.com/photo-1551776235-dde6d4829808?q=80&w=1200&auto=format&fit=crop'
            ], [
                'position' => 1,
            ]);

            // 20 rooms per hotel
            foreach (range(1,20) as $r) {
                $room = Room::firstOrCreate(
                    [
                        'hotel_id' => $hotel->id,
                        'name' => 'Chambre '.str_pad((string)$r, 2, '0', STR_PAD_LEFT),
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'type' => 'Burkina Faso', // demandé: type Burkina Faso
                        'price_per_night' => rand(15000, 90000),
                        'description' => 'Chambre lumineuse et climatisée, idéale pour les séjours au Burkina Faso.',
                        'photo' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200&auto=format&fit=crop',
                        'capacity' => rand(1, 4),
                        'available' => true,
                        'created_at' => now(), 'updated_at' => now(),
                    ]
                );

                RoomPhoto::firstOrCreate([
                    'room_id' => $room->id,
                    'path' => 'https://images.unsplash.com/photo-1554995207-c18c203602cb?q=80&w=1200&auto=format&fit=crop'
                ], [
                    'position' => 1,
                ]);
            }
        }

        $this->command?->info('HotelsRoomsSeeder completed: 10 hotels, 20 rooms each (type: Burkina Faso).');
    }
}
