<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityPostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->text('content');
            $table->string('image')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
                
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            
            // Index pour les performances
            $table->index('user_id');
            $table->index('created_at');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('community_posts');
    }
}
