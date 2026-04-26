<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar seeders de roles primeiro
        $this->call([
            CargoSeeder::class,
            UsersSeeder::class,
            MateriasSeeder::class,
            EscolaridadeSeeder::class,
            AreaSeeder::class,
        ]);
    }
}
