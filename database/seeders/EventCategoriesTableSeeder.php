<?php

namespace Database\Seeders;

use App\Models\Site\EventCategory;
use Illuminate\Database\Seeder;

class EventCategoriesTableSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $categories = [
            // Catégories principales
            ['name' => 'Conférences', 'icon' => 'fas fa-microphone', 'description' => 'Conférences et séminaires sur divers sujets'],
            ['name' => 'Ateliers', 'icon' => 'fas fa-paint-brush', 'description' => 'Ateliers pratiques et formations'],
            ['name' => 'Expositions', 'icon' => 'fas fa-images', 'description' => 'Expositions artistiques et culturelles'],
            ['name' => 'Spectacles', 'icon' => 'fas fa-theater-masks', 'description' => 'Spectacles vivants et représentations'],
            ['name' => 'Festivals', 'icon' => 'fas fa-glass-cheers', 'description' => 'Festivals culturels et artistiques'],
            ['name' => 'Sport', 'icon' => 'fas fa-futbol', 'description' => 'Événements sportifs et compétitions'],
            ['name' => 'Gastronomie', 'icon' => 'fas fa-utensils', 'description' => 'Événements culinaires et dégustations'],
            ['name' => 'Technologie', 'icon' => 'fas fa-laptop-code', 'description' => 'Salons et conférences technologiques'],
            ['name' => 'Développement personnel', 'icon' => 'fas fa-brain', 'description' => 'Ateliers de développement personnel'],
            ['name' => 'Autre', 'icon' => 'fas fa-ellipsis-h', 'description' => 'Autres types d\'événements']
        ];

        foreach ($categories as $category) {
            EventCategory::firstOrCreate(
                ['name' => $category['name']],
                [
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

        // Ajout de sous-catégories
        $subcategories = [
            // Sous-catégories pour Conférences
            ['name' => 'Conférences scientifiques', 'parent_id' => 1, 'icon' => 'fas fa-atom', 'description' => 'Conférences sur des sujets scientifiques'],
            ['name' => 'Conférences d\'affaires', 'parent_id' => 1, 'icon' => 'fas fa-briefcase', 'description' => 'Conférences sur le monde des affaires'],
            
            // Sous-catégories pour Ateliers
            ['name' => 'Ateliers artistiques', 'parent_id' => 2, 'icon' => 'fas fa-palette', 'description' => 'Ateliers pratiques d\'art'],
            ['name' => 'Ateliers numériques', 'parent_id' => 2, 'icon' => 'fas fa-laptop', 'description' => 'Ateliers sur les outils numériques'],
            
            // Sous-catégories pour Expositions
            ['name' => 'Expositions d\'art contemporain', 'parent_id' => 3, 'icon' => 'fas fa-paint-brush', 'description' => 'Expositions d\'art contemporain'],
            ['name' => 'Expositions historiques', 'parent_id' => 3, 'icon' => 'fas fa-landmark', 'description' => 'Expositions sur des thèmes historiques']
        ];

        foreach ($subcategories as $subcategory) {
            EventCategory::firstOrCreate(
                ['name' => $subcategory['name']],
                [
                    'parent_id' => $subcategory['parent_id'],
                    'icon' => $subcategory['icon'],
                    'description' => $subcategory['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
