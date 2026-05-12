<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pintura', 'description' => 'Obras pictóricas y arte tradicional.'],
            ['name' => 'Ilustración', 'description' => 'Ilustración digital, editorial y conceptual.'],
            ['name' => 'Fotografía', 'description' => 'Fotografía artística, documental y creativa.'],
            ['name' => 'Escultura', 'description' => 'Obras tridimensionales y piezas físicas.'],
            ['name' => 'Arte digital', 'description' => 'Creaciones digitales, multimedia y experimentales.'],
            ['name' => 'Música', 'description' => 'Producciones musicales y proyectos sonoros.'],
            ['name' => 'Danza', 'description' => 'Expresión corporal y artes escénicas.'],
            ['name' => 'Teatro', 'description' => 'Representaciones escénicas y dramaturgia.'],
            ['name' => 'Diseño', 'description' => 'Diseño gráfico, visual y creativo.'],
            ['name' => 'Otros', 'description' => 'Otras disciplinas artísticas.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
