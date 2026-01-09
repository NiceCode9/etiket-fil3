<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['slug' => 'sample-tenant'],
            [
                'name' => 'Sample Tenant',
                'email' => 'tenant@example.com',
                'phone' => '081234567890',
                'address' => 'Jl. Sample No. 123, Surabaya',
                'is_active' => true,
            ]
        );

        Tenant::firstOrCreate(
            ['slug' => 'music-festival-org'],
            [
                'name' => 'Music Festival Organization',
                'email' => 'info@musicfest.com',
                'phone' => '082345678901',
                'address' => 'Jl. Festival No. 456, Jakarta',
                'is_active' => true,
            ]
        );

        Tenant::firstOrCreate(
            ['slug' => 'tech-summit-org'],
            [
                'name' => 'Tech Summit Organization',
                'email' => 'info@techsummit.com',
                'phone' => '083456789012',
                'address' => 'Jl. Tech No. 789, Bandung',
                'is_active' => true,
            ]
        );
    }
}
