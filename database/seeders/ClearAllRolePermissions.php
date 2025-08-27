<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAllRolePermissions extends Seeder
{
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET CONSTRAINTS ALL DEFERRED');
        
        // Vider les tables dans le bon ordre pour éviter les problèmes de contrainte
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        
        // Vider les tables principales
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
    }
}
