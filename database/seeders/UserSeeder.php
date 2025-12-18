<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);

        // Staff users
        User::create([
            'name' => 'Staff Scanner',
            'email' => 'staff1@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Staff Validator',
            'email' => 'staff2@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
