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
        Schema::table('sites', function (Blueprint $table) {
            $table->foreignUuid('manager_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->string('address')->nullable()->after('city');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->json('opening_hours')->nullable()->after('website');
            $table->boolean('is_active')->default(true)->after('opening_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn([
                'manager_id',
                'address',
                'phone',
                'email',
                'website',
                'opening_hours',
                'is_active'
            ]);
        });
    }
};
