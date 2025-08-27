<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_users', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role'); // owner, admin, manager, staff
            $table->json('permissions')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();
            
            $table->unique(['account_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_users');
    }
};
