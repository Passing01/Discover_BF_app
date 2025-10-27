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
            $table->foreignUuid('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_main')->default(false);
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->timestamps();
        });

        Schema::create('room_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_main')->default(false);
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'is_main']);
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
