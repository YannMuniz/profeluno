<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class SalaAulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalaAula::create([
            'id' => 1,
            'titulo' => 'Algoritmos',
            'descricao' => 'Aula de Algoritmos',
            'user_id' => 2,
            'data_hora_inicio' => '2026-03-16 20:03:00',
            'data_hora_fim' => '2026-03-16 22:03:00',
            'materia' => 'For, Foreach, While',
            'material_id' => 1,
            'qtd_alunos' => 20,
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'avaliacao' => 10.0,
            'status' => 'Ativa',
            'created_at' => now(),
            'updated_at' => now(),  
        ]);
    }
}
