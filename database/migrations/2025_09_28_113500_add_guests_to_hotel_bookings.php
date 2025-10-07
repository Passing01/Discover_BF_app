<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->integer('adults')->default(1)->after('room_id');
            $table->integer('children')->nullable()->after('adults');
            $table->text('special_requests')->nullable()->after('children');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropColumn(['adults', 'children', 'special_requests']);
        });
    }
};
