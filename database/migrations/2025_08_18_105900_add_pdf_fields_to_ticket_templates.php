<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('bg_image_path');
            $table->unsignedInteger('pdf_page_count')->nullable()->after('pdf_path');
            $table->json('overlay_fields')->nullable()->after('layout_json');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_templates', function (Blueprint $table) {
            $table->dropColumn(['pdf_path','pdf_page_count','overlay_fields']);
        });
    }
};
