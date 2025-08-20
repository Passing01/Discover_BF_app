<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('guide_contacts')) {
            Schema::create('guide_contacts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('site_id')->constrained('sites')->cascadeOnDelete();
                $table->foreignUuid('guide_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->text('message');
                $table->string('status', 32)->default('new'); // new, contacted, closed
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_contacts');
    }
};
