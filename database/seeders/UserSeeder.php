<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get tenant (assuming TenantSeeder runs first)
        $tenant = \App\Models\Tenant::where('slug', 'sample-tenant')->first();

        // Admin user (no tenant - super admin)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'tenant_id' => null,
        ]);

        // Staff users (with tenant)
        User::create([
            'name' => 'Staff Scanner',
            'email' => 'staff1@example.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant?->id,
        ]);

        User::create([
            'name' => 'Staff Validator',
            'email' => 'staff2@example.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant?->id,
        ]);
    }
}
