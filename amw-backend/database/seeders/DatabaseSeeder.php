<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Carga los datos iniciales necesarios para la aplicación.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
        ]);
    }
}
