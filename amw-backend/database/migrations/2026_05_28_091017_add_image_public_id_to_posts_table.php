<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade el identificador de Cloudinary a las publicaciones.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table
                ->string('image_public_id')
                ->nullable()
                ->after('image_url');
        });
    }

    /**
     * Deshace el cambio si se revierte la migración.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image_public_id');
        });
    }
};
