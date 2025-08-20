<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_templates', 'text_color')) {
                $table->string('text_color', 20)->nullable()->after('secondary_color');
            }
            if (!Schema::hasColumn('ticket_templates', 'overlay_color')) {
                $table->string('overlay_color', 20)->nullable()->after('bg_image_path');
            }
            if (!Schema::hasColumn('ticket_templates', 'overlay_opacity')) {
                $table->decimal('overlay_opacity', 3, 2)->nullable()->after('overlay_color');
            }
            if (!Schema::hasColumn('ticket_templates', 'logo_enabled')) {
                $table->boolean('logo_enabled')->default(false)->after('overlay_opacity');
            }
            if (!Schema::hasColumn('ticket_templates', 'logo_position')) {
                $table->string('logo_position', 20)->nullable()->default('top-right')->after('logo_enabled');
            }
            if (!Schema::hasColumn('ticket_templates', 'logo_size')) {
                $table->integer('logo_size')->nullable()->default(56)->after('logo_position');
            }
            if (!Schema::hasColumn('ticket_templates', 'corner_radius')) {
                $table->integer('corner_radius')->nullable()->default(16)->after('logo_size');
            }
            if (!Schema::hasColumn('ticket_templates', 'card_shadow_enabled')) {
                $table->boolean('card_shadow_enabled')->default(true)->after('corner_radius');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            // Keep columns; do not drop to avoid data loss
        });
    }
};
