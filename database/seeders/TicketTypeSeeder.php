<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Music Festival Tickets
        TicketType::create([
            'event_id' => 1,
            'name' => 'Early Bird',
            'description' => 'Limited early bird tickets with special discount',
            'price' => 500000,
            'quota' => 1000,
            'available_quota' => 450,
            'is_active' => true,
        ]);

        TicketType::create([
            'event_id' => 1,
            'name' => 'Regular',
            'description' => 'Standard admission ticket',
            'price' => 750000,
            'quota' => 5000,
            'available_quota' => 4200,
            'is_active' => true,
        ]);

        TicketType::create([
            'event_id' => 1,
            'name' => 'VIP',
            'description' => 'VIP access with exclusive benefits',
            'price' => 1500000,
            'quota' => 500,
            'available_quota' => 380,
            'is_active' => true,
        ]);

        // Tech Summit Tickets
        TicketType::create([
            'event_id' => 2,
            'name' => 'Student Pass',
            'description' => 'Special price for students (ID required)',
            'price' => 200000,
            'quota' => 500,
            'available_quota' => 320,
            'is_active' => true,
        ]);

        TicketType::create([
            'event_id' => 2,
            'name' => 'Professional Pass',
            'description' => 'Full access to all sessions',
            'price' => 500000,
            'quota' => 1000,
            'available_quota' => 750,
            'is_active' => true,
        ]);

        // Food Carnival Tickets
        TicketType::create([
            'event_id' => 3,
            'name' => 'Daily Pass',
            'description' => 'Access for one day',
            'price' => 50000,
            'quota' => 10000,
            'available_quota' => 8500,
            'is_active' => true,
        ]);

        TicketType::create([
            'event_id' => 3,
            'name' => 'Weekend Pass',
            'description' => 'Access for all days',
            'price' => 150000,
            'quota' => 3000,
            'available_quota' => 2400,
            'is_active' => true,
        ]);

        // Comedy Night Tickets
        TicketType::create([
            'event_id' => 4,
            'name' => 'Standard Seat',
            'description' => 'General seating',
            'price' => 300000,
            'quota' => 800,
            'available_quota' => 620,
            'is_active' => true,
        ]);

        TicketType::create([
            'event_id' => 4,
            'name' => 'Premium Seat',
            'description' => 'Front row seating with meet & greet',
            'price' => 750000,
            'quota' => 200,
            'available_quota' => 145,
            'is_active' => true,
        ]);
    }
}
