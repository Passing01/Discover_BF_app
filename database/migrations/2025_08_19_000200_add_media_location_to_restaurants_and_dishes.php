<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('city');
            }
            if (!Schema::hasColumn('restaurants', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('restaurants', 'map_url')) {
                $table->string('map_url')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('restaurants', 'gallery')) {
                $table->json('gallery')->nullable()->after('cover_image');
            }
            if (!Schema::hasColumn('restaurants', 'video_urls')) {
                $table->json('video_urls')->nullable()->after('gallery');
            }
        });

        Schema::table('dishes', function (Blueprint $table) {
            if (!Schema::hasColumn('dishes', 'gallery')) {
                $table->json('gallery')->nullable()->after('image_path');
            }
            if (!Schema::hasColumn('dishes', 'video_urls')) {
                $table->json('video_urls')->nullable()->after('gallery');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (Schema::hasColumn('restaurants', 'video_urls')) {
                $table->dropColumn('video_urls');
            }
            if (Schema::hasColumn('restaurants', 'gallery')) {
                $table->dropColumn('gallery');
            }
            if (Schema::hasColumn('restaurants', 'map_url')) {
                $table->dropColumn('map_url');
            }
            if (Schema::hasColumn('restaurants', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('restaurants', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });

        Schema::table('dishes', function (Blueprint $table) {
            if (Schema::hasColumn('dishes', 'video_urls')) {
                $table->dropColumn('video_urls');
            }
            if (Schema::hasColumn('dishes', 'gallery')) {
                $table->dropColumn('gallery');
            }
        });
    }
};
