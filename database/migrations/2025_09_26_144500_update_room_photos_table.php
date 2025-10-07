<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoomPhotosTable extends Migration
{
    public function up()
    {
        Schema::table('room_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('room_photos', 'is_main')) {
                $table->boolean('is_main')->default(false)->after('path');
            }
            
            if (!Schema::hasColumn('room_photos', 'uploaded_by')) {
                $table->foreignUuid('uploaded_by')->nullable()->after('is_main')->constrained('users')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('room_photos', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('uploaded_by');
            }
            
            if (!Schema::hasColumn('room_photos', 'caption')) {
                $table->string('caption')->nullable()->after('alt_text');
            }
            
            // On ne modifie pas la colonne position car elle existe déjà
            
            // Ajout d'index pour améliorer les performances
            if (!Schema::hasIndex('room_photos', ['room_id', 'is_main'])) {
                $table->index(['room_id', 'is_main']);
            }
        });
    }

    public function down()
    {
        Schema::table('room_photos', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['is_main', 'uploaded_by', 'alt_text', 'caption', 'position']);
            $table->dropIndex(['room_id', 'is_main']);
        });
    }
}
