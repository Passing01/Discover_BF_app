<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->integer('capacity');
            $table->integer('size')->nullable();
            $table->string('bed_type');
            $table->timestamps();
        });

        // Ajout de quelques types de chambres par défaut
        DB::table('room_types')->insert([
            ['name' => 'Standard', 'description' => 'Chambre standard avec équipements de base', 'base_price' => 100.00, 'capacity' => 2, 'size' => 20, 'bed_type' => 'double', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supérieure', 'description' => 'Chambre spacieuse avec vue', 'base_price' => 150.00, 'capacity' => 2, 'size' => 28, 'bed_type' => 'queen', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Familiale', 'description' => 'Idéale pour les familles', 'base_price' => 200.00, 'capacity' => 4, 'size' => 35, 'bed_type' => 'multiple', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suite', 'description' => 'Suite luxueuse avec espace salon', 'base_price' => 300.00, 'capacity' => 2, 'size' => 45, 'bed_type' => 'king', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('room_types');
    }
};
