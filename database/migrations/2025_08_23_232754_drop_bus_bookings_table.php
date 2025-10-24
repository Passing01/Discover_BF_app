<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('bus_bookings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire ici car nous ne voulons pas recréer la table en cas de rollback
        // car la migration suivante s'en chargera
    }
};
