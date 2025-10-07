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
        Schema::table('guides', function (Blueprint $table) {
            $table->dateTime('available_from')->nullable()->after('certified');
            $table->dateTime('available_to')->nullable()->after('available_from');
            $table->text('availability_note')->nullable()->after('available_to');
            $table->boolean('is_available')->default(false)->after('availability_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn([
                'available_from',
                'available_to',
                'availability_note',
                'is_available'
            ]);
        });
    }
};
