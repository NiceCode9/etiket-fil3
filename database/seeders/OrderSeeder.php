<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Paid orders
        Order::create([
            'order_number' => 'ORD-20241215-ABC123',
            'customer_id' => 1,
            'event_id' => 1,
            'total_amount' => 1500000,
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'payment_channel' => 'visa',
            'snap_token' => 'snap_token_abc123',
            'transaction_id' => 'TRX-123456789',
            'paid_at' => now()->subDays(5),
            'expired_at' => now()->subDays(4),
        ]);

        Order::create([
            'order_number' => 'ORD-20241216-DEF456',
            'customer_id' => 2,
            'event_id' => 1,
            'total_amount' => 2250000,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'payment_channel' => 'bca',
            'snap_token' => 'snap_token_def456',
            'transaction_id' => 'TRX-123456790',
            'paid_at' => now()->subDays(3),
            'expired_at' => now()->subDays(2),
        ]);

        Order::create([
            'order_number' => 'ORD-20241216-GHI789',
            'customer_id' => 3,
            'event_id' => 2,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'payment_method' => 'e_wallet',
            'payment_channel' => 'gopay',
            'snap_token' => 'snap_token_ghi789',
            'transaction_id' => 'TRX-123456791',
            'paid_at' => now()->subDays(2),
            'expired_at' => now()->subDay(),
        ]);

        Order::create([
            'order_number' => 'ORD-20241217-JKL012',
            'customer_id' => 4,
            'event_id' => 3,
            'total_amount' => 150000,
            'payment_status' => 'paid',
            'payment_method' => 'e_wallet',
            'payment_channel' => 'ovo',
            'snap_token' => 'snap_token_jkl012',
            'transaction_id' => 'TRX-123456792',
            'paid_at' => now()->subDay(),
            'expired_at' => now(),
        ]);

        Order::create([
            'order_number' => 'ORD-20241217-MNO345',
            'customer_id' => 5,
            'event_id' => 4,
            'total_amount' => 600000,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'payment_channel' => 'qris',
            'snap_token' => 'snap_token_mno345',
            'transaction_id' => 'TRX-123456793',
            'paid_at' => now()->subHours(12),
            'expired_at' => now()->addHours(12),
        ]);

        // Pending orders
        Order::create([
            'order_number' => 'ORD-20241217-PQR678',
            'customer_id' => 6,
            'event_id' => 1,
            'total_amount' => 750000,
            'payment_status' => 'pending',
            'payment_method' => 'bank_transfer',
            'payment_channel' => 'mandiri',
            'snap_token' => 'snap_token_pqr678',
            'transaction_id' => null,
            'paid_at' => null,
            'expired_at' => now()->addHours(20),
        ]);

        Order::create([
            'order_number' => 'ORD-20241217-STU901',
            'customer_id' => 7,
            'event_id' => 2,
            'total_amount' => 200000,
            'payment_status' => 'pending',
            'payment_method' => 'e_wallet',
            'payment_channel' => 'dana',
            'snap_token' => 'snap_token_stu901',
            'transaction_id' => null,
            'paid_at' => null,
            'expired_at' => now()->addHours(18),
        ]);

        // Expired order
        Order::create([
            'order_number' => 'ORD-20241215-VWX234',
            'customer_id' => 8,
            'event_id' => 1,
            'total_amount' => 1500000,
            'payment_status' => 'expired',
            'payment_method' => 'credit_card',
            'payment_channel' => 'mastercard',
            'snap_token' => 'snap_token_vwx234',
            'transaction_id' => null,
            'paid_at' => null,
            'expired_at' => now()->subDays(2),
        ]);

        // Failed order
        Order::create([
            'order_number' => 'ORD-20241216-YZA567',
            'customer_id' => 9,
            'event_id' => 3,
            'total_amount' => 50000,
            'payment_status' => 'failed',
            'payment_method' => 'bank_transfer',
            'payment_channel' => 'bni',
            'snap_token' => 'snap_token_yza567',
            'transaction_id' => null,
            'paid_at' => null,
            'expired_at' => now()->subDay(),
        ]);

        // Another paid order for war ticket
        Order::create([
            'order_number' => 'ORD-20241217-BCD890',
            'customer_id' => 10,
            'event_id' => 1,
            'total_amount' => 700000,
            'payment_status' => 'paid',
            'payment_method' => 'e_wallet',
            'payment_channel' => 'shopeepay',
            'snap_token' => 'snap_token_bcd890',
            'transaction_id' => 'TRX-123456794',
            'paid_at' => now()->subHours(6),
            'expired_at' => now()->addHours(18),
        ]);
    }
}
