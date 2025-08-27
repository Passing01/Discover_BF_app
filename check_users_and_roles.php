<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Fonction pour afficher les rôles d'un utilisateur
function getUserRoles($user) {
    $roleNames = [];
    foreach ($user->roles as $role) {
        $roleNames[] = $role->name;
    }
    return implode(', ', $roleNames);
}

// Fonction pour afficher les permissions d'un rôle
function getRolePermissions($role) {
    $permissionNames = [];
    foreach ($role->permissions as $permission) {
        $permissionNames[] = $permission->name;
    }
    return implode(', ', $permissionNames);
}

// Récupérer tous les utilisateurs avec leurs rôles
$users = User::with('roles')->get();

echo "=== Utilisateurs et leurs rôles ===\n";

foreach ($users as $user) {
    echo "- {$user->email} (ID: {$user->id})\n";
    echo "  Rôle: {$user->role}\n";
    echo "  Rôles Spatie: " . getUserRoles($user) . "\n\n";
}

echo "=== Rôles disponibles ===\n";

// Récupérer tous les rôles avec leurs permissions
$roles = Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "- {$role->name} (ID: {$role->id})\n";
    echo "  Permissions: " . getRolePermissions($role) . "\n\n";
}
