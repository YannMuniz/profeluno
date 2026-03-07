<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'id' => 1,
            'user_id' => 1,
            'nome_admin' => 'Eduardo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Admin::create([
            'id' => 2,
            'user_id' => 2,
            'nome_admin' => 'Yann',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
