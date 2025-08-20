<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('driver_id')->constrained('users')->onDelete('cascade');
            $table->string('license_plate');
            $table->string('model');
            $table->string('color');
            $table->boolean('available')->default(true);
            $table->decimal('price_per_km', 8, 2);
            $table->timestamps();
        });

        Schema::create('rides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('taxi_id')->constrained()->onDelete('cascade');
            $table->dateTime('ride_date');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->decimal('distance_km', 8, 2);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['requested', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('requested');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
        Schema::dropIfExists('taxis');
    }
};
