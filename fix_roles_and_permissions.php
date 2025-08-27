<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

// Désactiver les contraintes de clé étrangère
DB::statement('SET CONSTRAINTS ALL DEFERRED');

// Vider les tables de rôles et permissions
DB::table('model_has_roles')->truncate();
DB::table('model_has_permissions')->truncate();
DB::table('role_has_permissions')->truncate();
DB::table('permissions')->truncate();
DB::table('roles')->truncate();

// Réactiver les contraintes de clé étrangère
DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

echo "Tables de rôles et permissions vidées.\n";

// Créer les rôles et permissions de base
$roles = [
    'admin' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage posts', 'manage users', 'manage settings',
        'manage all restaurants', 'manage all sites', 'manage all flights', 'manage all bus routes'
    ],
    'tourist' => [
        'view posts', 'create posts', 'edit own posts', 'delete own posts',
        'book services', 'manage own bookings', 'write reviews'
    ],
    'guide' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage own tours', 'view bookings', 'update booking status'
    ],
    'event_organizer' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage own events', 'manage event bookings', 'update event status'
    ],
    'hotel_manager' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage own hotels', 'manage room bookings', 'update booking status'
    ],
    'restaurant_manager' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage own restaurants', 'manage table bookings', 'update booking status'
    ],
    'site_manager' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage tourist sites', 'manage site visits', 'update visit status'
    ],
    'vol_manager' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage flights', 'manage flight bookings', 'update flight status'
    ],
    'bus_manager' => [
        'view posts', 'create posts', 'edit posts', 'delete posts',
        'manage bus routes', 'manage bus bookings', 'update bus status'
    ]
];

// Créer les permissions
$allPermissions = [];
$permissionNames = [];

// D'abord, collecter tous les noms de permissions uniques
foreach ($roles as $permissions) {
    foreach ($permissions as $permission) {
        $permissionNames[$permission] = true;
    }
}

// Ensuite, créer chaque permission avec un UUID
foreach (array_keys($permissionNames) as $permissionName) {
    $permission = new Permission([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => $permissionName,
        'guard_name' => 'web',
    ]);
    $permission->save();
    $allPermissions[$permissionName] = $permission;
    echo "Permission créée: {$permissionName}\n";
}

echo "Permissions créées.\n";

// Créer les rôles et assigner les permissions
$roleModels = [];
foreach ($roles as $roleName => $permissions) {
    // Créer le rôle avec un UUID manuel
    $role = new Role([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => $roleName,
        'guard_name' => 'web',
    ]);
    $role->save();
    
    // Récupérer les modèles de permissions pour ce rôle
    $permissionModels = [];
    foreach ($permissions as $permissionName) {
        if (isset($allPermissions[$permissionName])) {
            $permissionModels[] = $allPermissions[$permissionName];
        }
    }
    
    // Ajouter les permissions manuellement pour éviter les problèmes de typage UUID
    if (!empty($permissionModels)) {
        $permissionIds = [];
        foreach ($permissionModels as $permission) {
            $permissionIds[] = $permission->id;
        }
        
        // Insérer manuellement les relations dans la table role_has_permissions
        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }
    }
    
    $roleModels[$roleName] = $role;
    echo "Rôle '{$roleName}' créé avec les permissions: " . implode(', ', $permissions) . "\n";
}

echo "Rôles créés avec leurs permissions.\n";

// Assigner les rôles aux utilisateurs
$users = User::all();

foreach ($users as $user) {
    if (!empty($user->role) && isset($roleModels[$user->role])) {
        $user->syncRoles([$user->role]);
        echo "Rôle '{$user->role}' assigné à l'utilisateur {$user->email}\n";
    } else {
        // Par défaut, assigner le rôle 'tourist' si le rôle n'est pas reconnu
        if (empty($user->role)) {
            $user->role = 'tourist';
            $user->save();
            $user->syncRoles(['tourist']);
            echo "Rôle 'tourist' assigné par défaut à l'utilisateur {$user->email}\n";
        } else {
            echo "Attention: Le rôle '{$user->role}' n'existe pas pour l'utilisateur {$user->email}\n";
        }
    }
}

echo "Synchronisation des rôles terminée.\n";
