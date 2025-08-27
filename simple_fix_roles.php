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

// Créer les rôles de base
$roles = [
    'admin',
    'tourist',
    'guide',
    'event_organizer',
    'hotel_manager',
    'restaurant_manager',
    'site_manager',
    'vol_manager',
    'bus_manager'
];

foreach ($roles as $roleName) {
    $role = new Role();
    $role->id = (string) \Illuminate\Support\Str::uuid();
    $role->name = $roleName;
    $role->guard_name = 'web';
    $role->save();
    echo "Rôle créé: {$roleName}\n";
}

// Assigner le rôle admin à l'utilisateur admin
$adminUser = User::where('email', 'admin@discoverbf.test')->first();
if ($adminUser) {
    $adminUser->syncRoles(['admin']);
    echo "Rôle admin assigné à admin@discoverbf.test\n";
}

echo "Opération terminée.\n";
