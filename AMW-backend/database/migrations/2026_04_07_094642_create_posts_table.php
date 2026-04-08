<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Relación con usuario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Relación con categoría
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            // Contenido
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();

            // Tipo de contenido
            $table->enum('type', ['obra', 'evento', 'producto'])->default('obra');

            // Publicación
            $table->boolean('is_published')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
