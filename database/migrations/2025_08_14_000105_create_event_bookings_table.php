<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create the table if it does not already exist (some legacy migrations create it)
        if (!Schema::hasTable('event_bookings')) {
            Schema::create('event_bookings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('event_id')->constrained('events')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('buyer_name');
                $table->string('buyer_email');
                $table->string('status')->default('confirmed');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // No-op: Do not drop the table here to avoid removing a table created by earlier migrations
    }
};
