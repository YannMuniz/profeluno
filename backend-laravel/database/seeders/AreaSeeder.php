<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Area::create([
            'id' => 1,
            'nome_area' => 'Saúde',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 2,
            'nome_area' => 'Educação',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 3,
            'nome_area' => 'Tecnologia',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 4,
            'nome_area' => 'Administração',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 5,
            'nome_area' => 'Engenharia',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 6,
            'nome_area' => 'Ciências Exatas',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 7,
            'nome_area' => 'Ciências Humanas',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 8,
            'nome_area' => 'Ciências Biológicas',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 9,
            'nome_area' => 'Artes',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Area::create([
            'id' => 10,
            'nome_area' => 'Outros',
            'situacao_area' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
