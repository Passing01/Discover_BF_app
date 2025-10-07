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
        // D'abord, ajouter les colonnes comme nullable
        Schema::table('hotel_photos', function (Blueprint $table) {
            $table->boolean('is_main')->default(false)->after('hotel_id');
            $table->string('original_name')->nullable()->after('path');
            $table->string('mime_type')->nullable()->after('original_name');
            $table->unsignedInteger('size')->nullable()->after('mime_type');
        });

        // Mettre à jour les enregistrements existants avec des valeurs par défaut
        \DB::table('hotel_photos')
            ->whereNull('original_name')
            ->update([
                'original_name' => 'unknown.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 0
            ]);

        // Ensuite, modifier les colonnes pour les rendre non nullables
        Schema::table('hotel_photos', function (Blueprint $table) {
            $table->string('original_name')->nullable(false)->change();
            $table->string('mime_type')->nullable(false)->change();
            $table->unsignedInteger('size')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_photos', function (Blueprint $table) {
            $table->dropColumn(['is_main', 'original_name', 'mime_type', 'size']);
        });
    }
};
