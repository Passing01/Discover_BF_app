<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('business_name');
            $table->string('legal_name');
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('website')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('XOF');
            $table->string('logo_path')->nullable();
            $table->string('status')->default('pending'); // pending, active, suspended, rejected
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
