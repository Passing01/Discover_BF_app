<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // In case a previous failed migration left the table behind without a record
        Schema::dropIfExists('dish_orders');

        Schema::create('dish_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('restaurant_id');
            $table->uuid('dish_id');
            $table->unsignedInteger('quantity');
            $table->string('delivery_address', 500);
            $table->timestamp('delivery_time')->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('status', 30)->default('pending');
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->cascadeOnDelete();
            $table->foreign('dish_id')->references('id')->on('dishes')->cascadeOnDelete();
            $table->index(['restaurant_id','dish_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_orders');
    }
};
