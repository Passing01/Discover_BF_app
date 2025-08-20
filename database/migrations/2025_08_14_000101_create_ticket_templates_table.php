<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('bg_image_path')->nullable();
            $table->string('logo_placement')->nullable();
            $table->string('image_placement')->nullable();
            $table->string('shape')->nullable();
            $table->string('font_family')->nullable();
            $table->string('qr_position')->nullable();
            $table->unsignedSmallInteger('qr_size')->nullable();
            $table->json('layout_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_templates');
    }
};
