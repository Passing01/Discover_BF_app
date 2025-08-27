<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Trouver l'utilisateur admin
$admin = User::where('email', 'admin@example.com')->first();

if ($admin) {
    // Mettre à jour le rôle
    $admin->role = 'admin';
    $admin->save();
    
    echo "Le rôle de l'utilisateur admin@example.com a été mis à jour à 'admin'.\n";
} else {
    echo "Utilisateur admin@example.com non trouvé.\n";
}
