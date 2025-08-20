<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('location');
            $table->decimal('ticket_price', 10, 2);
            $table->string('category');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });

        Schema::create('guides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->json('spoken_languages');
            $table->decimal('hourly_rate', 8, 2);
            $table->text('description');
            $table->boolean('certified')->default(false);
            $table->timestamps();
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('guide_id')->constrained('guides')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('duration');
            $table->decimal('price', 10, 2);
            $table->json('points_of_interest');
            $table->timestamps();
        });

        Schema::create('tour_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('tour_id')->constrained()->onDelete('cascade');
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        // Optional: keep Event->bookings() relationship working
        Schema::create('event_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('event_id')->constrained()->onDelete('cascade');
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_bookings');
        Schema::dropIfExists('tour_bookings');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('guides');
        Schema::dropIfExists('events');
    }
};
