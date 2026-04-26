<?php

namespace Database\Seeders;

use App\Models\Escolaridade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class EscolaridadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Escolaridade::create([
            'id' => 1,
            'nome_escolaridade' => 'Ensino Fundamental',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Escolaridade::create([
            'id' => 2,
            'nome_escolaridade' => 'Ensino Médio',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Escolaridade::create([
            'id' => 3,
            'nome_escolaridade' => 'Ensino Superior',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Escolaridade::create([
            'id' => 4,
            'nome_escolaridade' => 'Pós-Graduação',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Escolaridade::create([
            'id' => 5,
            'nome_escolaridade' => 'Mestrado',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Escolaridade::create([
            'id' => 6,
            'nome_escolaridade' => 'Doutorado',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Escolaridade::create([
            'id' => 7,
            'nome_escolaridade' => 'Pós-Doutorado',
            'situacao_escolaridade' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
