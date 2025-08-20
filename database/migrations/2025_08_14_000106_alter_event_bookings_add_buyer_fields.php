<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing columns defensively if the table already existed without them
        if (Schema::hasTable('event_bookings')) {
            if (!Schema::hasColumn('event_bookings', 'buyer_name')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->string('buyer_name')->after('user_id');
                });
            }
            if (!Schema::hasColumn('event_bookings', 'buyer_email')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->string('buyer_email')->after('buyer_name');
                });
            }
            if (!Schema::hasColumn('event_bookings', 'status')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->string('status')->default('confirmed')->after('buyer_email');
                });
            }
            if (!Schema::hasColumn('event_bookings', 'total_amount')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->decimal('total_amount', 12, 2)->default(0)->after('status');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_bookings')) {
            if (Schema::hasColumn('event_bookings', 'total_amount')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->dropColumn('total_amount');
                });
            }
            if (Schema::hasColumn('event_bookings', 'status')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
            if (Schema::hasColumn('event_bookings', 'buyer_email')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->dropColumn('buyer_email');
                });
            }
            if (Schema::hasColumn('event_bookings', 'buyer_name')) {
                Schema::table('event_bookings', function (Blueprint $table) {
                    $table->dropColumn('buyer_name');
                });
            }
        }
    }
};
