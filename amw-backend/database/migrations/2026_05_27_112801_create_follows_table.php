<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();

            /*
             * Usuario que pulsa "Seguir".
             * Ejemplo: Derek sigue a Elena -> follower_id = Derek.
             */
            $table->foreignId('follower_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Usuario que recibe el seguimiento.
             * Ejemplo: Derek sigue a Elena -> followed_id = Elena.
             */
            $table->foreignId('followed_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            /*
             * Impide que el mismo usuario siga dos veces al mismo perfil.
             */
            $table->unique(['follower_id', 'followed_id']);
        });

        /*
         * Tu proyecto utiliza PostgreSQL.
         * Esta restricción impide también desde base de datos que
         * un usuario pueda seguirse a sí mismo.
         */
        DB::statement(
            'ALTER TABLE follows
             ADD CONSTRAINT follows_no_self_follow
             CHECK (follower_id <> followed_id)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
