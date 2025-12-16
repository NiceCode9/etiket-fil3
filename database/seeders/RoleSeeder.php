<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $qrScanner = Role::create(['name' => 'qr_scanner']);
        $wristbandValidator = Role::create(['name' => 'wristband_validator']);

        $scannerUser = User::create([
            'name' => 'QR Scanner',
            'email' => 'scanner@example.com',
            'password' => bcrypt('password'),
        ]);
        $scannerUser->assignRole('qr_scanner');

        $validatorUser = User::create([
            'name' => 'Wristband Validator',
            'email' => 'validator@example.com',
            'password' => bcrypt('password'),
        ]);
        $validatorUser->assignRole('wristband_validator');
    }
}
