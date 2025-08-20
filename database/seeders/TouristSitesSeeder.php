<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TouristSitesSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            ['name' => 'Musée National du Burkina Faso', 'city' => 'Ouagadougou', 'category' => 'Culture', 'photo_url' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.36566, 'lng' => -1.53388],
            ['name' => 'Village Artisanal de Ouaga', 'city' => 'Ouagadougou', 'category' => 'Artisanat', 'photo_url' => 'https://images.unsplash.com/photo-1597006434721-65e1f08c3c87?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.358, 'lng' => -1.512],
            ['name' => 'Mosquée de Dioulassoba', 'city' => 'Bobo-Dioulasso', 'category' => 'Patrimoine', 'photo_url' => 'https://images.unsplash.com/photo-1544989164-31dc3c645987?q=80&w=1200&auto=format&fit=crop', 'lat' => 11.1771, 'lng' => -4.2976],
            ['name' => 'Cascades de Karfiguéla', 'city' => 'Banfora', 'category' => 'Nature', 'photo_url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?q=80&w=1200&auto=format&fit=crop', 'lat' => 10.6500, 'lng' => -4.7667],
            ['name' => 'Dômes de Fabédougou', 'city' => 'Banfora', 'category' => 'Géologie', 'photo_url' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200&auto=format&fit=crop', 'lat' => 10.6333, 'lng' => -4.8000],
            ['name' => 'Parc Urbain Bangr-Weoogo', 'city' => 'Ouagadougou', 'category' => 'Nature', 'photo_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.396, 'lng' => -1.524],
            ['name' => 'Parc National du W', 'city' => 'Est', 'category' => 'Faune', 'photo_url' => 'https://images.unsplash.com/photo-1474511320723-9a56873867b5?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.500, 'lng' => 2.000],
            ['name' => 'Ruines de Loropéni', 'city' => 'Loropéni', 'category' => 'UNESCO', 'photo_url' => 'https://images.unsplash.com/photo-1549880338-65ddcdfd017b?q=80&w=1200&auto=format&fit=crop', 'lat' => 10.2833, 'lng' => -3.5167],
            ['name' => 'Lac de Tengrela', 'city' => 'Banfora', 'category' => 'Nature', 'photo_url' => 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?q=80&w=1200&auto=format&fit=crop', 'lat' => 10.6167, 'lng' => -4.8333],
            ['name' => 'Bazoulé (lac aux crocodiles)', 'city' => 'Bazoulé', 'category' => 'Faune', 'photo_url' => 'https://images.unsplash.com/photo-1543877087-ebf71fde2be1?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.486, 'lng' => -1.878],
            ['name' => 'Place des Cinéastes', 'city' => 'Ouagadougou', 'category' => 'Culture', 'photo_url' => 'https://images.unsplash.com/photo-1536766820879-059fec98ec1e?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.369, 'lng' => -1.536],
            ['name' => 'Cathédrale de Ouagadougou', 'city' => 'Ouagadougou', 'category' => 'Patrimoine', 'photo_url' => 'https://images.unsplash.com/photo-1558980664-10ea78cb28d9?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.368, 'lng' => -1.528],
            ['name' => 'Peaks de Sindou', 'city' => 'Sindou', 'category' => 'Géologie', 'photo_url' => 'https://images.unsplash.com/photo-1523345863762-5888c8b32f67?q=80&w=1200&auto=format&fit=crop', 'lat' => 10.6667, 'lng' => -5.1667],
            ['name' => 'Marché de Bobo-Dioulasso', 'city' => 'Bobo-Dioulasso', 'category' => 'Artisanat', 'photo_url' => 'https://images.unsplash.com/photo-1556909212-d5e9476b3f27?q=80&w=1200&auto=format&fit=crop', 'lat' => 11.178, 'lng' => -4.297],
            ['name' => 'Guinguette de Bobo', 'city' => 'Bobo-Dioulasso', 'category' => 'Nature', 'photo_url' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?q=80&w=1200&auto=format&fit=crop', 'lat' => 11.15, 'lng' => -4.30],
            ['name' => 'Vallon de la Marahoué', 'city' => 'Koudougou', 'category' => 'Nature', 'photo_url' => 'https://images.unsplash.com/photo-1431794062232-2a99a5431c6c?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.256, 'lng' => -2.362],
            ['name' => 'Sculptures de Laongo', 'city' => 'Laongo', 'category' => 'Art', 'photo_url' => 'https://images.unsplash.com/photo-1578926374373-6664f59e0d4b?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.47, 'lng' => -1.18],
            ['name' => 'Réserve de Pô-Nazinga', 'city' => 'Pô', 'category' => 'Faune', 'photo_url' => 'https://images.unsplash.com/photo-1546182990-dffeafbe841d?q=80&w=1200&auto=format&fit=crop', 'lat' => 11.15, 'lng' => -1.15],
            ['name' => 'Forêt classée de Dindéresso', 'city' => 'Bobo-Dioulasso', 'category' => 'Nature', 'photo_url' => 'https://images.unsplash.com/photo-1508780709619-79562169bc64?q=80&w=1200&auto=format&fit=crop', 'lat' => 11.20, 'lng' => -4.45],
            ['name' => 'Sanctuaire de Yagma', 'city' => 'Ouagadougou', 'category' => 'Religion', 'photo_url' => 'https://images.unsplash.com/photo-1549641201-8a9093221df1?q=80&w=1200&auto=format&fit=crop', 'lat' => 12.51, 'lng' => -1.58],
        ];

        $rows = [];
        foreach ($sites as $s) {
            $rows[] = [
                'id' => (string) Str::uuid(),
                'name' => $s['name'],
                'city' => $s['city'],
                'category' => $s['category'],
                'description' => null,
                'price_min' => null,
                'price_max' => null,
                'latitude' => $s['lat'],
                'longitude' => $s['lng'],
                'photo_url' => $s['photo_url'],
                'created_at' => now(), 'updated_at' => now(),
            ];
        }

        DB::table('sites')->insertOrIgnore($rows);
        $this->command?->info('TouristSitesSeeder completed: seeded '.count($rows).' sites.');
    }
}
