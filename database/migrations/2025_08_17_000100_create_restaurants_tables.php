<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id')->nullable()->index(); // users.id with role restaurant
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->decimal('avg_price', 10, 2)->nullable();
            $table->float('rating')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('cover_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dishes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('restaurant_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_path')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('restaurant_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('restaurant_id')->index();
            $table->dateTime('reservation_at');
            $table->unsignedInteger('party_size')->default(2);
            $table->string('status')->default('requested'); // requested, confirmed, cancelled
            $table->string('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_reservations');
        Schema::dropIfExists('dishes');
        Schema::dropIfExists('restaurants');
    }
};
