<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearRolePermissions extends Seeder
{
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        
        // Vider les tables pivot
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        
        // Vider les tables principales
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
    }
}
