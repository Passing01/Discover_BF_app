<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_billings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            
            // Billing information
            $table->string('billing_name');
            $table->string('billing_email');
            $table->string('billing_phone');
            $table->string('billing_address');
            $table->string('billing_city');
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code');
            $table->string('billing_country');
            
            // Payment method information (encrypted in real implementation)
            $table->string('payment_method_type')->nullable(); // credit_card, mobile_money, etc.
            $table->string('payment_method_last_four')->nullable();
            $table->string('payment_method_expiry')->nullable();
            
            // Billing cycle
            $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, annually
            $table->date('next_billing_date')->nullable();
            
            // Tax information
            $table->string('tax_identification_number')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_billings');
    }
};
