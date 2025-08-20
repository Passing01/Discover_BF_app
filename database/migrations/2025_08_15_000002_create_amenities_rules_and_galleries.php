<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('amenity_hotel', function (Blueprint $table) {
            $table->uuid('hotel_id');
            $table->uuid('amenity_id');
            $table->primary(['hotel_id','amenity_id']);
        });

        Schema::create('hotel_rule', function (Blueprint $table) {
            $table->uuid('hotel_id');
            $table->uuid('rule_id');
            $table->primary(['hotel_id','rule_id']);
        });

        Schema::create('hotel_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('hotel_id');
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('room_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_photos');
        Schema::dropIfExists('hotel_photos');
        Schema::dropIfExists('hotel_rule');
        Schema::dropIfExists('amenity_hotel');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('amenities');
    }
};
