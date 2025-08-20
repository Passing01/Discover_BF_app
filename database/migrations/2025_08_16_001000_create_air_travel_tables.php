<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('iata_code', 8)->nullable();
            $table->string('city');
            $table->string('country')->default('Burkina Faso');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });

        Schema::create('flights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('airline')->nullable();
            $table->string('flight_number')->nullable();
            $table->foreignUuid('origin_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignUuid('destination_airport_id')->constrained('airports')->onDelete('cascade');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->decimal('base_price', 10, 2);
            $table->unsignedInteger('seats_total')->default(150);
            $table->unsignedInteger('seats_available')->default(150);
            $table->timestamps();
        });

        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('flight_id')->constrained('flights')->onDelete('cascade');
            $table->unsignedInteger('passengers_count');
            $table->enum('class', ['economy','business','first'])->default('economy');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending','confirmed','cancelled'])->default('pending');
            // contact info
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            // passengers details (localized names)
            $table->json('passengers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('airports');
    }
};
