<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Désactiver la journalisation des requêtes pour une meilleure lisibilité
DB::disableQueryLog();

echo "Début de l'assignation des rôles...\n";

// Récupérer tous les utilisateurs
$users = User::all();

foreach ($users as $user) {
    try {
        if (empty($user->role)) {
            // Si l'utilisateur n'a pas de rôle, lui attribuer 'tourist' par défaut
            $user->role = 'tourist';
            $user->save();
        }
        
        // Vérifier si l'utilisateur a déjà des rôles
        if ($user->roles->isEmpty()) {
            // Trouver le rôle correspondant
            $role = Role::where('name', $user->role)->first();
            
            if ($role) {
                // Utiliser la méthode assignRole du modèle User
                $user->assignRole($role);
                echo "Rôle '{$user->role}' assigné à l'utilisateur {$user->email}\n";
            } else {
                echo "Attention: Le rôle '{$user->role}' n'existe pas pour l'utilisateur {$user->email}\n";
            }
        } else {
            echo "L'utilisateur {$user->email} a déjà des rôles assignés.\n";
        }
    } catch (\Exception $e) {
        echo "Erreur avec l'utilisateur {$user->email} (ID: {$user->id}): " . $e->getMessage() . "\n";
    }
}

echo "Opération terminée.\n";
