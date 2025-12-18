<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'full_name' => 'Budi Santoso',
                'email' => 'nicecode9@gmail.com',
                'phone_number' => '081234567890',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670001',
            ],
            [
                'full_name' => 'Siti Nurhaliza',
                'email' => 'siti.nur@email.com',
                'phone_number' => '082345678901',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670002',
            ],
            [
                'full_name' => 'Ahmad Zainudin',
                'email' => 'ahmad.z@email.com',
                'phone_number' => '083456789012',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670003',
            ],
            [
                'full_name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@email.com',
                'phone_number' => '084567890123',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670004',
            ],
            [
                'full_name' => 'Rizky Febian',
                'email' => 'rizky.f@email.com',
                'phone_number' => '085678901234',
                'identity_type' => 'sim',
                'identity_number' => 'SIM1234567890',
            ],
            [
                'full_name' => 'Nina Zahra',
                'email' => 'nina.zahra@email.com',
                'phone_number' => '086789012345',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670005',
            ],
            [
                'full_name' => 'Fajar Ramadhan',
                'email' => 'fajar.r@email.com',
                'phone_number' => '087890123456',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670006',
            ],
            [
                'full_name' => 'Linda Kusuma',
                'email' => 'linda.kusuma@email.com',
                'phone_number' => '088901234567',
                'identity_type' => 'passport',
                'identity_number' => 'A1234567',
            ],
            [
                'full_name' => 'Eko Prasetyo',
                'email' => 'eko.p@email.com',
                'phone_number' => '089012345678',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670007',
            ],
            [
                'full_name' => 'Rani Puspita',
                'email' => 'rani.puspita@email.com',
                'phone_number' => '081123456789',
                'identity_type' => 'ktp',
                'identity_number' => '3578012345670008',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
