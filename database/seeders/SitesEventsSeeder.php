<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class SitesEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sites
        $sites = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Musée National du Burkina Faso',
                'city' => 'Ouagadougou',
                'category' => 'Culture',
                'description' => 'Collections ethnographiques et expositions sur le patrimoine du Burkina.',
                'price_min' => 1000,
                'price_max' => 3000,
                'latitude' => 12.36566,
                'longitude' => -1.53388,
                'photo_url' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Village Artisanal de Ouaga',
                'city' => 'Ouagadougou',
                'category' => 'Artisanat',
                'description' => 'Ateliers et boutiques d’artisans locaux: bronzes, batiks, cuir, bijoux.',
                'price_min' => 0,
                'price_max' => 0,
                'latitude' => 12.358,
                'longitude' => -1.512,
                'photo_url' => 'https://images.unsplash.com/photo-1597006434721-65e1f08c3c87?q=80&w=1200&auto=format&fit=crop',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Mosquée de Dioulassoba',
                'city' => 'Bobo-Dioulasso',
                'category' => 'Patrimoine',
                'description' => 'Architecture en banco emblématique, site historique.',
                'price_min' => 1000,
                'price_max' => 2000,
                'latitude' => 11.1771,
                'longitude' => -4.2976,
                'photo_url' => 'https://images.unsplash.com/photo-1544989164-31dc3c645987?q=80&w=1200&auto=format&fit=crop',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        DB::table('sites')->insertOrIgnore($sites);

        // Events (next month)
        $now = Carbon::now();
        $hasTitleColumn = Schema::hasColumn('events', 'title');
        $hasNameColumn = Schema::hasColumn('events', 'name');
        
        $events = [];
        
        // Premier événement
        $event1 = [
            'id' => (string) Str::uuid(),
            'name' => 'Concert live — Semaine de la Musique',
            'title' => 'Concert live — Semaine de la Musique',
            'description' => 'Un concert live exceptionnel avec des artistes locaux et internationaux',
            'start_date' => $now->copy()->addDays(10),
            'end_date' => $now->copy()->addDays(10)->addHours(3),
            'location' => 'Maison du Peuple, Ouagadougou',
            'ticket_price' => 10000.00,
            'city' => 'Ouagadougou',
            'category' => 'Musique',
            'venue' => 'Maison du Peuple',
            'starts_at' => $now->copy()->addDays(10),
            'ends_at' => $now->copy()->addDays(10)->addHours(3),
            'price_min' => 5000,
            'price_max' => 15000,
            'latitude' => 12.366,
            'longitude' => -1.53,
            'photo_url' => 'https://images.unsplash.com/photo-1511193311914-0346f16efe90?q=80&w=1200&auto=format&fit=crop',
            'created_at' => now(),
            'updated_at' => now(),
            [
                'id' => (string) Str::uuid(),
                'title' => 'Concert live — Semaine de la Musique',
                'name' => 'Concert live — Semaine de la Musique',
                'description' => 'Concert live — Semaine de la Musique',
                'city' => 'Ouagadougou',
                'category' => 'Musique',
                'venue' => 'Maison du Peuple',
                'start_date' => $now->copy()->addDays(10),
                'end_date' => $now->copy()->addDays(10)->addHours(3),
                'location' => 'Maison du Peuple, Ouagadougou',
                'ticket_price' => 5000,
                'price_min' => 5000,
                'price_max' => 15000,
                'latitude' => 12.366,
                'longitude' => -1.53,
                'photo_url' => 'https://images.unsplash.com/photo-1511193311914-0346f16efe90?q=80&w=1200&auto=format&fit=crop',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'title' => 'Festival des Arts et de la Culture',
                'name' => 'Festival des Arts et de la Culture',
                'description' => 'Festival des Arts et de la Culture',
                'city' => 'Bobo-Dioulasso',
                'category' => 'Culture',
                'venue' => 'Place Tiefo Amoro',
                'start_date' => $now->copy()->addDays(18),
                'end_date' => $now->copy()->addDays(20),
                'location' => 'Bobo-Dioulasso',
                'ticket_price' => 10000,
                'price_min' => 0,
                'price_max' => 5000,
                'latitude' => 11.177,
                'longitude' => -4.297,
                'photo_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=1200&auto=format&fit=crop',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        // If events table has organizer_id and it's NOT NULL, assign a user id
        $addOrganizer = Schema::hasColumn('events', 'organizer_id');
        $organizerId = null;
        if ($addOrganizer) {
            try {
                $organizerId = DB::table('users')->orderBy('id')->value('id');
            } catch (\Throwable $e) {
                $organizerId = null;
            }
            foreach ($events as &$e) {
                $e['organizer_id'] = $organizerId; // may be null if column is nullable; otherwise DB will error which signals schema mismatch
            }
            unset($e);
        }

        DB::table('events')->insertOrIgnore($events);
    }
}
