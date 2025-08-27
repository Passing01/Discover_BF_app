<?php

namespace Database\Seeders;

use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunityPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des publications d'exemple
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('Aucun utilisateur trouvé. Veuillez d\'abord exécuter le seeder d\'utilisateurs.');
            return;
        }

        $posts = [
            [
                'content' => 'Bonjour à tous ! Je prévois un voyage à Ouagadougou le mois prochain. Des conseils sur les endroits à visiter ?',
                'image' => null,
            ],
            [
                'content' => 'Quelqu\'un a déjà testé le restaurant "La Belle Étoile" ? Je cherche des avis avant d\'y aller ce week-end !',
                'image' => null,
            ],
            [
                'content' => 'Super expérience au Parc Bangr-Weoogo aujourd\'hui ! Les paysages sont magnifiques et l\'accueil est chaleureux.',
                'image' => 'community/posts/sample1.jpg',
            ],
            [
                'content' => 'Recherche compagnon de voyage pour explorer le Burkina en décembre. Je pars pour 2 semaines.',
                'image' => null,
            ],
            [
                'content' => 'Conseils pour un premier voyage au Burkina ? Je viens de France et je ne sais pas par où commencer...',
                'image' => null,
            ]
        ];

        foreach ($posts as $postData) {
            $user = $users->random();
            
            CommunityPost::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'content' => $postData['content'],
                'image' => $postData['image'],
                'likes_count' => rand(0, 50),
                'comments_count' => rand(0, 20),
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Publications de la communauté ajoutées avec succès !');
    }
}
