<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Afficher les rôles disponibles
echo "=== Rôles disponibles ===\n";
foreach (Role::all() as $role) {
    echo "- {$role->name} (ID: {$role->id})\n";
}

// Afficher les utilisateurs et leurs rôles
echo "\n=== Utilisateurs et leurs rôles ===\n";
foreach (User::with('roles')->get() as $user) {
    $roles = $user->roles->pluck('name')->implode(', ');
    echo "- {$user->email} (ID: {$user->id}) - Rôle: {$user->role} - Rôles Spatie: [{$roles}]\n";
}
