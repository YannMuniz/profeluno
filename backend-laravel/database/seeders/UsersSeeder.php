<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'email' => 'eduassis29@gmail.com',
            'password' => md5('Capim12'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'id' => 2,
            'email' => 'yannmunizbarbosa@gmail.com',
            'password' => md5('26112004'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
