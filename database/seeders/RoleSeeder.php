<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Tenant;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $tenantAdmin = Role::firstOrCreate(['name' => 'tenant_admin']);
        $qrScanner = Role::firstOrCreate(['name' => 'qr_scanner']);
        $wristbandValidator = Role::firstOrCreate(['name' => 'wristband_validator']);

        // Create a sample tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'sample-tenant'],
            [
                'name' => 'Sample Tenant',
                'email' => 'tenant@example.com',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // Create super admin user (no tenant)
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        if (!$superAdminUser->hasRole('super_admin')) {
            $superAdminUser->assignRole('super_admin');
        }

        // Create tenant admin user
        $tenantAdminUser = User::firstOrCreate(
            ['email' => 'tenantadmin@example.com'],
            [
                'name' => 'Tenant Admin',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$tenantAdminUser->hasRole('tenant_admin')) {
            $tenantAdminUser->assignRole('tenant_admin');
        }

        // Create QR scanner user
        $scannerUser = User::firstOrCreate(
            ['email' => 'scanner@example.com'],
            [
                'name' => 'QR Scanner',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$scannerUser->hasRole('qr_scanner')) {
            $scannerUser->assignRole('qr_scanner');
        }

        // Create wristband validator user
        $validatorUser = User::firstOrCreate(
            ['email' => 'validator@example.com'],
            [
                'name' => 'Wristband Validator',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
            ]
        );
        if (!$validatorUser->hasRole('wristband_validator')) {
            $validatorUser->assignRole('wristband_validator');
        }
    }
}
