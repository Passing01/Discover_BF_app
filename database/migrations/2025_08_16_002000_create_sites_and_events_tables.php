<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('city');
                $table->string('category')->nullable(); // Culture, Nature, Gastronomie, etc.
                $table->text('description')->nullable();
                $table->unsignedInteger('price_min')->nullable();
                $table->unsignedInteger('price_max')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('photo_url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('city');
                $table->string('category')->nullable(); // Musique, Culture, Festival, etc.
                $table->string('venue')->nullable();
                $table->dateTime('starts_at');
                $table->dateTime('ends_at')->nullable();
                $table->unsignedInteger('price_min')->nullable();
                $table->unsignedInteger('price_max')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('photo_url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('sites');
    }
};
