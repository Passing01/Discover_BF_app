<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

// Fonction pour obtenir l'ID d'un rôle par son nom
function getRoleIdByName($roleName) {
    $role = Role::where('name', $roleName)->first();
    return $role ? $role->id : null;
}

// Récupérer tous les utilisateurs
$users = User::all();

foreach ($users as $user) {
    if (!empty($user->role)) {
        // Vérifier si l'utilisateur a déjà des rôles
        if ($user->roles->isEmpty()) {
            $roleId = getRoleIdByName($user->role);
            
            if ($roleId) {
                // Utiliser une requête SQL brute avec des paramètres pour les UUID
                $sql = "INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)";
                $params = [
                    $roleId,
                    'App\\\\Models\\\\User',
                    $user->id
                ];
                
                try {
                    DB::insert($sql, $params);
                    echo "Rôle '{$user->role}' assigné à l'utilisateur {$user->email}\n";
                } catch (\Exception $e) {
                    echo "Erreur lors de l'assignation du rôle '{$user->role}' à l'utilisateur {$user->email}: " . $e->getMessage() . "\n";
                }
            } else {
                echo "Attention: Le rôle '{$user->role}' n'existe pas pour l'utilisateur {$user->email}\n";
            }
        } else {
            echo "L'utilisateur {$user->email} a déjà des rôles assignés.\n";
        }
    } else {
        // Si l'utilisateur n'a pas de rôle, lui attribuer le rôle 'tourist' par défaut
        $user->role = 'tourist';
        $user->save();
        
        $roleId = getRoleIdByName('tourist');
        if ($roleId) {
            $sql = "INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)";
            $params = [
                $roleId,
                'App\\\\Models\\\\User',
                $user->id
            ];
            
            try {
                DB::insert($sql, $params);
                echo "Rôle 'tourist' assigné par défaut à l'utilisateur {$user->email}\n";
            } catch (\Exception $e) {
                echo "Erreur lors de l'assignation du rôle 'tourist' à l'utilisateur {$user->email}: " . $e->getMessage() . "\n";
            }
        }
    }
}

echo "Opération terminée.\n";
