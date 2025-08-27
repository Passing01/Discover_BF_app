<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurant_reservations', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('order_items');
            $table->string('payment_intent_id')->nullable()->after('payment_status');
            $table->integer('amount_paid')->nullable()->after('payment_intent_id');
            $table->index('payment_status');
            $table->index('payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_reservations', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['payment_intent_id']);
            $table->dropColumn(['payment_status', 'payment_intent_id', 'amount_paid']);
        });
    }
};
