<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('license_plate')->unique();
            $table->unsignedInteger('capacity');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('bus_trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bus_id')->constrained('buses')->onDelete('cascade');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('seats_total');
            $table->unsignedInteger('seats_available');
            $table->timestamps();
        });

        Schema::create('bus_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('bus_trip_id')->constrained('bus_trips')->onDelete('cascade');
            $table->unsignedInteger('seats')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending','confirmed','cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_bookings');
        Schema::dropIfExists('bus_trips');
        Schema::dropIfExists('buses');
    }
};
