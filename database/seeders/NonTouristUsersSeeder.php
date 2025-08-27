<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NonTouristUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'DiscoverBF',
                'email' => 'admin@discoverbf.test',
                'role' => 'admin',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Moussa',
                'last_name' => 'Traoré',
                'email' => 'guide@discoverbf.test',
                'role' => 'guide',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Awa',
                'last_name' => 'Ouédraogo',
                'email' => 'organizer@discoverbf.test',
                'role' => 'event_organizer',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Issa',
                'last_name' => 'Kaboré',
                'email' => 'hotel@discoverbf.test',
                'role' => 'hotel_manager',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Fatim',
                'last_name' => 'Sawadogo',
                'email' => 'restaurant@discoverbf.test',
                'role' => 'restaurant_manager',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Boubakar',
                'last_name' => 'Zongo',
                'email' => 'site@discoverbf.test',
                'role' => 'site_manager',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Kadidia',
                'last_name' => 'Ouedraogo',
                'email' => 'vol@discoverbf.test',
                'role' => 'vol_manager',
                'role_onboarded_at' => now(),
            ],
            [
                'first_name' => 'Amadou',
                'last_name' => 'Boro',
                'email' => 'bus@discoverbf.test',
                'role' => 'bus_manager',
                'role_onboarded_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make('password'), // default password
                    'role' => $data['role'] ?? 'tourist',
                    'role_onboarded_at' => $data['role_onboarded_at'] ?? null,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
        }
    }
}
