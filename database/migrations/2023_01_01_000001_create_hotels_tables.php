<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('phone');
            $table->string('email');
            $table->text('description');
            $table->integer('stars');
            $table->string('photo')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->foreignUuid('manager_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('room_number', 20)->nullable();
            $table->foreignUuid('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->decimal('price_per_night', 10, 2);
            $table->text('description');
            $table->string('photo')->nullable();
            $table->integer('capacity');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });

        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable()->unique();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('room_id')->constrained()->onDelete('cascade');
            $table->uuid('payment_id')->nullable();
            $table->integer('adults')->default(1);
            $table->integer('children')->nullable();
            $table->text('special_requests')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('hotels');
    }
};
