<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Récupérer tous les utilisateurs
$users = User::all();

foreach ($users as $user) {
    // Vérifier si l'utilisateur a déjà des rôles Spatie
    if ($user->roles->isEmpty() && !empty($user->role)) {
        // Trouver le rôle correspondant
        $role = Role::where('name', $user->role)->first();
        
        if ($role) {
            // Assigner le rôle à l'utilisateur
            $user->assignRole($role);
            echo "Rôle '{$user->role}' assigné à l'utilisateur {$user->email}\n";
        } else {
            echo "Attention: Le rôle '{$user->role}' n'existe pas pour l'utilisateur {$user->email}\n";
        }
    }
}

echo "Synchronisation terminée.\n";
