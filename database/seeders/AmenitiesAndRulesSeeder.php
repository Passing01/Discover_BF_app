<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;
use App\Models\StayRule;

class AmenitiesAndRulesSeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Wi‑Fi'],
            ['name' => 'Climatisation'],
            ['name' => 'Parking'],
            ['name' => 'Piscine'],
            ['name' => 'Petit‑déjeuner'],
            ['name' => 'Télévision'],
            ['name' => 'Cuisine'],
            ['name' => 'Lave‑linge'],
        ];
        foreach ($amenities as $a) {
            Amenity::firstOrCreate(['name' => $a['name']], $a);
        }

        $rules = [
            ['name' => 'Non‑fumeur'],
            ['name' => 'Animaux interdits'],
            ['name' => 'Pas de fête'],
            ['name' => 'Check‑in après 14h'],
            ['name' => 'Check‑out avant 12h'],
        ];
        foreach ($rules as $r) {
            StayRule::firstOrCreate(['name' => $r['name']], $r);
        }
    }
}
