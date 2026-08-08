<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Compte Administrateur / Staff
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrateur Campus360',
                'password' => Hash::make('123'),
                'role' => 'admin',
            ]
        );

        // 2. Compte Étudiant de test
        User::updateOrCreate(
            ['email' => 'etudiant@gmail.com'],
            [
                'name' => 'Étudiant Campus360',
                'password' => Hash::make('123'),
                'role' => 'student',
            ]
        );
    }
}
