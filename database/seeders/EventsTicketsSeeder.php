<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class EventsTicketsSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = DB::table('users')->where('email', 'organizer@discoverbf.test')->first();
        if (!$organizer) {
            $this->command?->warn('Organizer user not found (organizer@discoverbf.test). Seeding events without organizer_id.');
        }

        $now = Carbon::now();
        $cities = ['Ouagadougou','Bobo-Dioulasso','Koudougou','Banfora','Ouahigouya'];
        $categories = ['Musique','Culture','Festival','Conférence','Sport'];
        $venues = ['Maison du Peuple','Stade du 4-Août','Place des Artistes','Centre Culturel','Palais des Sports'];

        $events = [];
        foreach (range(1, 20) as $i) {
            $start = $now->copy()->addDays(5 + $i)->setTime(rand(14,20), [0,30][array_rand([0,30])]);
            $end = $start->copy()->addHours(rand(2,5));
            $city = $cities[array_rand($cities)];
            $cat = $categories[array_rand($categories)];
            $venue = $venues[array_rand($venues)];

            $eventName = "$cat — Évènement $i";
            $row = [
                'id' => (string) Str::uuid(),
                // base schema from 2025_08_16_002000_create_sites_and_events_tables
                'title' => $eventName,
                'name' => $eventName, // Champ obligatoire pour la table events
                'description' => $eventName, // Champ obligatoire pour la table events
                'city' => $city,
                'category' => $cat,
                'venue' => $venue,
                'location' => $venue,
                'ticket_price' => rand(0, 5000),
                'start_date' => $start,
                'end_date' => $end,
                'price_min' => rand(0, 5000),
                'price_max' => rand(10000, 30000),
                'latitude' => 12.36 + (mt_rand(-50, 50) / 1000),
                'longitude' => -1.53 + (mt_rand(-50, 50) / 1000),
                'photo_url' => 'https://images.unsplash.com/photo-1515165562835-c3b8c0f0b3a0?q=80&w=1200&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // If extended columns exist, fill them too
            if (Schema::hasColumn('events', 'image_path')) {
                $row['image_path'] = $row['photo_url'];
            }
            if (Schema::hasColumn('events', 'organizer_id') && $organizer) {
                $row['organizer_id'] = $organizer->id;
            }

            $events[] = $row;
        }

        DB::table('events')->insertOrIgnore($events);

        // Create 2 ticket types per event if ticket types table exists
        if (Schema::hasTable('event_ticket_types')) {
            $ticketTypes = [];
            $eventsIds = DB::table('events')->pluck('id');
            foreach ($eventsIds as $eventId) {
                $ticketTypes[] = [
                    'id' => (string) Str::uuid(),
                    'event_id' => $eventId,
                    'name' => 'Standard',
                    'description' => 'Accès général',
                    'price' => rand(0, 5000),
                    'currency' => 'XOF',
                    'capacity' => 200,
                    'sales_start_at' => $now->copy()->subDays(3),
                    'sales_end_at' => $now->copy()->addDays(25),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $ticketTypes[] = [
                    'id' => (string) Str::uuid(),
                    'event_id' => $eventId,
                    'name' => 'VIP',
                    'description' => 'Accès privilégié + zone réservée',
                    'price' => rand(10000, 30000),
                    'currency' => 'XOF',
                    'capacity' => 50,
                    'sales_start_at' => $now->copy()->subDays(3),
                    'sales_end_at' => $now->copy()->addDays(25),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('event_ticket_types')->insertOrIgnore($ticketTypes);
        } else {
            $this->command?->warn('Table event_ticket_types not found. Skipping ticket types.');
        }

        $this->command?->info('EventsTicketsSeeder completed: 20 events with photos and ticket types.');
    }
}
