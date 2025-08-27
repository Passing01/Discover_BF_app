<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModerationColumnsToCommunityPosts extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('community_posts', function (Blueprint $table) {
            // Ajouter la colonne is_active si elle n'existe pas déjà
            if (!Schema::hasColumn('community_posts', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('content');
            }
            
            // Ajouter la colonne deleted_by si elle n'existe pas déjà
            if (!Schema::hasColumn('community_posts', 'deleted_by')) {
                $table->foreignUuid('deleted_by')
                      ->nullable()
                      ->after('is_active')
                      ->constrained('users')
                      ->onDelete('set null');
            }
            
            // Ajouter un index sur is_active pour les performances
            if (!Schema::hasIndex('community_posts', 'community_posts_is_active_index')) {
                $table->index('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('community_posts', function (Blueprint $table) {
            // Supprimer l'index si nécessaire
            if (Schema::hasIndex('community_posts', 'community_posts_is_active_index')) {
                $table->dropIndex('community_posts_is_active_index');
            }
            
            // Supprimer la contrainte de clé étrangère si elle existe
            if (Schema::hasColumn('community_posts', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
                $table->dropColumn('deleted_by');
            }
            
            // Supprimer la colonne is_active si elle existe
            if (Schema::hasColumn('community_posts', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
}
