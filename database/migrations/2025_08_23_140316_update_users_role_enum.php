<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la contrainte existe avant de la supprimer
        $constraintExists = DB::select("SELECT * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND CONSTRAINT_NAME = 'users_role_check'");
            
        if (count($constraintExists) > 0) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
        }
        
        // Mettre à jour la colonne role avec les nouveaux rôles
        DB::statement("ALTER TABLE users 
            MODIFY COLUMN role VARCHAR(255) DEFAULT 'tourist'");
            
        // Ajouter une nouvelle contrainte check avec les rôles mis à jour
        DB::statement("ALTER TABLE users 
            ADD CONSTRAINT users_role_check 
            CHECK (role IN ('tourist', 'guide', 'admin', 'hotel_manager', 'driver', 'event_organizer', 'restaurant_manager', 'site_manager', 'vol_manager', 'bus_manager', 'user'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
