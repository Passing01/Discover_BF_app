<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityLikesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('community_likes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('post_id');
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
                
            $table->foreign('post_id')
                ->references('id')
                ->on('community_posts')
                ->onDelete('cascade');
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['user_id', 'post_id']);
            
            // Index pour les performances
            $table->index('user_id');
            $table->index('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('community_likes');
    }
}
