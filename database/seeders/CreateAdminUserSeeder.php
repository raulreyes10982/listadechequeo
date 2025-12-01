<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );
    }
}
