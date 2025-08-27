<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

// Désactiver la journalisation des requêtes pour une meilleure lisibilité
DB::disableQueryLog();

echo "Début de la correction des rôles...\n";

// Vérifier si la table model_has_roles existe
$tableExists = DB::select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'model_has_roles')");
if (!$tableExists[0]->exists) {
    echo "La table model_has_roles n'existe pas. Vérifiez votre installation de Spatie Laravel Permission.\n";
    exit(1);
}

// Vider la table model_has_roles
try {
    DB::table('model_has_roles')->truncate();
    echo "Table model_has_roles vidée.\n";
} catch (\Exception $e) {
    echo "Erreur lors de la vidange de la table model_has_roles: " . $e->getMessage() . "\n";
    exit(1);
}

// Récupérer tous les utilisateurs
$users = User::all();
$roles = Role::all()->keyBy('name');

echo "\nAssignation des rôles aux utilisateurs :\n";
foreach ($users as $user) {
    try {
        if (empty($user->role)) {
            $user->role = 'tourist';
            $user->save();
        }
        
        if (!isset($roles[$user->role])) {
            echo "  - [ERREUR] Le rôle '{$user->role}' n'existe pas pour l'utilisateur {$user->email}\n";
            continue;
        }
        
        $roleId = $roles[$user->role]->id;
        
        // Utiliser une requête SQL brute avec des paramètres nommés
        $sql = "INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (:role_id, :model_type, :model_id)";
        
        $params = [
            'role_id' => $roleId,
            'model_type' => 'App\\\\Models\\\\User',
            'model_id' => $user->id
        ];
        
        try {
            DB::insert($sql, $params);
            echo "  - [SUCCÈS] Rôle '{$user->role}' assigné à l'utilisateur {$user->email}\n";
        } catch (\Exception $e) {
            echo "  - [ERREUR] Impossible d'assigner le rôle '{$user->role}' à l'utilisateur {$user->email}: " . $e->getMessage() . "\n";
            
            // Afficher plus de détails sur l'erreur
            if (strpos($e->getMessage(), 'syntaxe en entrée invalide pour le type uuid') !== false) {
                echo "    [DÉTAILS] Problème de format UUID. Role ID: {$roleId}, User ID: {$user->id}\n";
            }
        }
    } catch (\Exception $e) {
        echo "  - [ERREUR] Erreur avec l'utilisateur {$user->email} (ID: {$user->id}): " . $e->getMessage() . "\n";
    }
}

echo "\nVérification des rôles assignés :\n";

// Vérifier les rôles assignés
foreach ($users as $user) {
    $assignedRoles = DB::select("SELECT r.name FROM roles r 
                               INNER JOIN model_has_roles mhr ON r.id = mhr.role_id 
                               WHERE mhr.model_id = ? AND mhr.model_type = ?", 
                               [$user->id, 'App\\\\Models\\\\User']);
    
    if (empty($assignedRoles)) {
        echo "  - [ATTENTION] L'utilisateur {$user->email} n'a aucun rôle assigné.\n";
    } else {
        $roleNames = array_map(function($r) { return $r->name; }, $assignedRoles);
        echo "  - [OK] {$user->email}: " . implode(', ', $roleNames) . "\n";
    }
}

echo "\nOpération terminée.\n";
