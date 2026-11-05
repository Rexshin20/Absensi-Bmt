<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@absensibmt.test'],
            [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
