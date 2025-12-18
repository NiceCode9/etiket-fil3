<?php

namespace Database\Seeders;

use App\Models\WarTicket;
use Illuminate\Database\Seeder;

class WarTicketSeeder extends Seeder
{
    public function run(): void
    {
        // Active war ticket for Music Festival Early Bird
        WarTicket::create([
            'ticket_type_id' => 1,
            // 'war_name' => 'Flash Sale - 100 Tickets Only!',
            'war_price' => 350000,
            'war_quota' => 100,
            'war_available_quota' => 65,
            'start_time' => now()->subHours(2),
            'end_time' => now()->addHours(22),
            'is_active' => true,
        ]);

        // Upcoming war ticket for Music Festival Regular
        WarTicket::create([
            'ticket_type_id' => 2,
            // 'war_name' => 'Weekend War - Special Price',
            'war_price' => 600000,
            'war_quota' => 500,
            'war_available_quota' => 500,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(3),
            'is_active' => true,
        ]);

        // Past war ticket (expired)
        WarTicket::create([
            'ticket_type_id' => 1,
            // 'war_name' => 'Super Early Bird',
            'war_price' => 300000,
            'war_quota' => 50,
            'war_available_quota' => 0,
            'start_time' => now()->subWeeks(2),
            'end_time' => now()->subWeeks(1),
            'is_active' => false,
        ]);

        // Active war for Food Carnival
        WarTicket::create([
            'ticket_type_id' => 6,
            // 'war_name' => 'Daily Flash Deal',
            'war_price' => 35000,
            'war_quota' => 200,
            'war_available_quota' => 180,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(5),
            'is_active' => true,
        ]);
    }
}
