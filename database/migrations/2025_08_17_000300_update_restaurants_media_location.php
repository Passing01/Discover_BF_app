<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('city');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('map_url')->nullable()->after('longitude');
            $table->json('gallery')->nullable()->after('cover_image'); // array of image URLs/paths
            $table->json('video_urls')->nullable()->after('gallery');  // array of video URLs (youtube/vimeo/direct)
        });

        Schema::table('dishes', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('image_path'); // array of image URLs/paths
            $table->json('video_urls')->nullable()->after('gallery');  // array of video URLs
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['latitude','longitude','map_url','gallery','video_urls']);
        });
        Schema::table('dishes', function (Blueprint $table) {
            $table->dropColumn(['gallery','video_urls']);
        });
    }
};
