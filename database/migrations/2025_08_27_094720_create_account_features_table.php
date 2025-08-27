<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_features', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('feature'); // hotel, restaurant, transport, event, flight
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->unique(['account_id', 'feature']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_features');
    }
};
