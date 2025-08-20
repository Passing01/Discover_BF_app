<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_bookings') && Schema::hasColumn('event_bookings', 'booking_date')) {
            // Make booking_date nullable to be compatible with the new booking flow
            if (DB::connection() instanceof \Illuminate\Database\PostgresConnection) {
                DB::statement('ALTER TABLE event_bookings ALTER COLUMN booking_date DROP NOT NULL');
            } else {
                DB::statement('ALTER TABLE event_bookings MODIFY booking_date DATE NULL');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_bookings') && Schema::hasColumn('event_bookings', 'booking_date')) {
            // Revert to NOT NULL if needed (may fail if data contains nulls)
            if (DB::connection() instanceof \Illuminate\Database\PostgresConnection) {
                DB::statement('ALTER TABLE event_bookings ALTER COLUMN booking_date SET NOT NULL');
            } else {
                DB::statement('ALTER TABLE event_bookings MODIFY booking_date DATE NOT NULL');
            }
        }
    }
};
