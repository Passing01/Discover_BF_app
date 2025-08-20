<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'title')) {
                    $table->string('title')->nullable();
                }
                if (!Schema::hasColumn('events', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('events', 'category')) {
                    $table->string('category')->nullable();
                }
                if (!Schema::hasColumn('events', 'venue')) {
                    $table->string('venue')->nullable();
                }
                if (!Schema::hasColumn('events', 'starts_at')) {
                    $table->dateTime('starts_at')->nullable();
                }
                if (!Schema::hasColumn('events', 'ends_at')) {
                    $table->dateTime('ends_at')->nullable();
                }
                if (!Schema::hasColumn('events', 'price_min')) {
                    $table->unsignedInteger('price_min')->nullable();
                }
                if (!Schema::hasColumn('events', 'price_max')) {
                    $table->unsignedInteger('price_max')->nullable();
                }
                if (!Schema::hasColumn('events', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable();
                }
                if (!Schema::hasColumn('events', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable();
                }
                if (!Schema::hasColumn('events', 'photo_url')) {
                    $table->string('photo_url')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'photo_url')) {
                    $table->dropColumn('photo_url');
                }
                if (Schema::hasColumn('events', 'longitude')) {
                    $table->dropColumn('longitude');
                }
                if (Schema::hasColumn('events', 'latitude')) {
                    $table->dropColumn('latitude');
                }
                if (Schema::hasColumn('events', 'price_max')) {
                    $table->dropColumn('price_max');
                }
                if (Schema::hasColumn('events', 'price_min')) {
                    $table->dropColumn('price_min');
                }
                if (Schema::hasColumn('events', 'ends_at')) {
                    $table->dropColumn('ends_at');
                }
                if (Schema::hasColumn('events', 'starts_at')) {
                    $table->dropColumn('starts_at');
                }
                if (Schema::hasColumn('events', 'venue')) {
                    $table->dropColumn('venue');
                }
                if (Schema::hasColumn('events', 'category')) {
                    $table->dropColumn('category');
                }
                if (Schema::hasColumn('events', 'city')) {
                    $table->dropColumn('city');
                }
            });
        }
    }
};
