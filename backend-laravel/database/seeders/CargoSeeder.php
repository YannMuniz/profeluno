<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cargo::create([
            'id' => 1,
            'nome_cargo' => 'Aluno',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Cargo::create([
            'id' => 2,
            'nome_cargo' => 'Professor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Cargo::create([
            'id' => 3,
            'nome_cargo' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
