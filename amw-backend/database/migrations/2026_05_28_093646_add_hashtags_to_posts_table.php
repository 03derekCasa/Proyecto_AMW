<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade las etiquetas utilizadas por las publicaciones.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table
                ->json('hashtags')
                ->nullable()
                ->after('description');
        });
    }

    /**
     * Elimina la columna si se revierte la migración.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('hashtags');
        });
    }
};
