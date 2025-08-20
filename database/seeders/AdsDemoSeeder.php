<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ad;

class AdsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $ads = [
            [
                'placement' => 'restaurants_top',
                'title' => 'Promo -10% ce week-end',
                'image_path' => null,
                'target_url' => '#',
                'cta_text' => 'Réservez maintenant',
                'enabled' => true,
                'weight' => 5,
            ],
            [
                'placement' => 'restaurant_show_top',
                'title' => 'Livraison disponible',
                'image_path' => null,
                'target_url' => '#',
                'cta_text' => 'Voir conditions',
                'enabled' => true,
                'weight' => 3,
            ],
            [
                'placement' => 'restaurant_reserve_sidebar',
                'title' => 'Soirée Jazz - Vendredi',
                'image_path' => null,
                'target_url' => '#',
                'cta_text' => 'Réserver une table',
                'enabled' => true,
                'weight' => 4,
            ],
            [
                'placement' => 'restaurant_reservation_sidebar',
                'title' => 'Partenaire Vin local',
                'image_path' => null,
                'target_url' => '#',
                'cta_text' => 'Découvrir',
                'enabled' => true,
                'weight' => 2,
            ],
        ];

        foreach ($ads as $a) {
            Ad::create($a);
        }
    }
}
