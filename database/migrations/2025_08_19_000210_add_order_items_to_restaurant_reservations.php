<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurant_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurant_reservations', 'order_items')) {
                $table->json('order_items')->nullable()->after('special_requests');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_reservations', 'order_items')) {
                $table->dropColumn('order_items');
            }
        });
    }
};
