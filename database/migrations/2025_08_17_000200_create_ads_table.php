<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('placement'); // e.g., home_top, restaurant_sidebar, tourist_events_top
            $table->string('title')->nullable();
            $table->string('image_path')->nullable();
            $table->string('target_url')->nullable();
            $table->string('cta_text')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('weight')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
