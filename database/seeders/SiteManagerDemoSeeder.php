<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SiteBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SiteManagerDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le gestionnaire de sites de test
        $siteManager = User::where('email', 'site@discoverbf.test')->first();

        if (!$siteManager) {
            $this->command->warn('Aucun gestionnaire de sites trouvé. Veuillez d\'abord exécuter NonTouristUsersSeeder.');
            return;
        }

        // Créer un site de test
        $site = Site::updateOrCreate(
            ['name' => 'Ruines de Loropéni'],
            [
                'manager_id' => $siteManager->id,
                'city' => 'Loropéni',
                'category' => 'patrimoine',
                'description' => 'Site archéologique classé au patrimoine mondial de l\'UNESCO, les ruines de Loropéni sont un ensemble de murs de pierre datant du 11ème siècle.',
                'price_min' => 1000,
                'price_max' => 5000,
                'latitude' => 10.2983,
                'longitude' => -3.5622,
                'address' => 'Loropéni, Province du Poni, Burkina Faso',
                'phone' => '+226 70 12 34 56',
                'email' => 'contact@ruines-loropeni.bf',
                'website' => 'https://ruines-loropeni.bf',
                'opening_hours' => json_encode([
                    'lundi' => ['open' => '08:00', 'close' => '18:00'],
                    'mardi' => ['open' => '08:00', 'close' => '18:00'],
                    'mercredi' => ['open' => '08:00', 'close' => '18:00'],
                    'jeudi' => ['open' => '08:00', 'close' => '18:00'],
                    'vendredi' => ['open' => '08:00', 'close' => '18:00'],
                    'samedi' => ['open' => '09:00', 'close' => '17:00'],
                    'dimanche' => ['open' => '09:00', 'close' => '15:00'],
                ]),
                'is_active' => true,
                'photo_url' => 'https://example.com/ruines-loropeni.jpg',
            ]
        );

        // Créer des réservations de test
        if (User::count() > 0) {
            $users = User::where('role', 'tourist')->take(3)->get();
            
            foreach ($users as $index => $user) {
                $visitDate = now()->addDays($index + 1);
                
                SiteBooking::create([
                    'site_id' => $site->id,
                    'user_id' => $user->id,
                    'visit_date' => $visitDate,
                    'visitors_count' => rand(1, 5),
                    'total_amount' => $site->price_min * (1 + rand(1, 5)),
                    'status' => $index === 0 ? 'pending' : ($index === 1 ? 'confirmed' : 'completed'),
                    'special_requests' => $index === 0 ? 'Visite guidée en français' : null,
                ]);
            }
        }

        $this->command->info('Site de démonstration pour le gestionnaire créé avec succès !');
    }
}
