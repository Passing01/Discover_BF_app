<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ar_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->enum('media_type', ['3d_model', 'video', 'audio', 'image']);
            $table->string('media_file');
            $table->timestamps();
        });

        Schema::create('user_ar_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('ar_location_id')->constrained('ar_locations')->onDelete('cascade');
            $table->dateTime('visit_date');
            $table->enum('status', ['discovered', 'completed', 'shared'])->default('discovered');
            $table->timestamps();
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->integer('points_required');
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('badge_id')->constrained()->onDelete('cascade');
            $table->dateTime('earned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('user_ar_progress');
        Schema::dropIfExists('ar_locations');
    }
};
