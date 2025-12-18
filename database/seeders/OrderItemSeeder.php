<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Order 1 - Customer 1 (2 VIP tickets)
        OrderItem::create([
            'order_id' => 1,
            'ticket_type_id' => 3,
            'war_ticket_id' => null,
            'quantity' => 2,
            'price' => 1500000,
            'subtotal' => 1500000,
        ]);

        // Order 2 - Customer 2 (3 Regular tickets)
        OrderItem::create([
            'order_id' => 2,
            'ticket_type_id' => 2,
            'war_ticket_id' => null,
            'quantity' => 3,
            'price' => 750000,
            'subtotal' => 2250000,
        ]);

        // Order 3 - Customer 3 (1 Professional Pass)
        OrderItem::create([
            'order_id' => 3,
            'ticket_type_id' => 5,
            'war_ticket_id' => null,
            'quantity' => 1,
            'price' => 500000,
            'subtotal' => 500000,
        ]);

        // Order 4 - Customer 4 (1 Weekend Pass)
        OrderItem::create([
            'order_id' => 4,
            'ticket_type_id' => 7,
            'war_ticket_id' => null,
            'quantity' => 1,
            'price' => 150000,
            'subtotal' => 150000,
        ]);

        // Order 5 - Customer 5 (2 Standard Seats)
        OrderItem::create([
            'order_id' => 5,
            'ticket_type_id' => 8,
            'war_ticket_id' => null,
            'quantity' => 2,
            'price' => 300000,
            'subtotal' => 600000,
        ]);

        // Order 6 - Customer 6 (1 Regular ticket - Pending)
        OrderItem::create([
            'order_id' => 6,
            'ticket_type_id' => 2,
            'war_ticket_id' => null,
            'quantity' => 1,
            'price' => 750000,
            'subtotal' => 750000,
        ]);

        // Order 7 - Customer 7 (1 Student Pass - Pending)
        OrderItem::create([
            'order_id' => 7,
            'ticket_type_id' => 4,
            'war_ticket_id' => null,
            'quantity' => 1,
            'price' => 200000,
            'subtotal' => 200000,
        ]);

        // Order 8 - Customer 8 (1 VIP ticket - Expired)
        OrderItem::create([
            'order_id' => 8,
            'ticket_type_id' => 3,
            'war_ticket_id' => null,
            'quantity' => 1,
            'price' => 1500000,
            'subtotal' => 1500000,
        ]);

        // Order 9 - Customer 9 (1 Daily Pass - Failed)
        OrderItem::create([
            'order_id' => 9,
            'ticket_type_id' => 6,
            'war_ticket_id' => null,
            'quantity' => 1,
            'price' => 50000,
            'subtotal' => 50000,
        ]);

        // Order 10 - Customer 10 (2 Early Bird tickets from War Ticket)
        OrderItem::create([
            'order_id' => 10,
            'ticket_type_id' => 1,
            'war_ticket_id' => 1,
            'quantity' => 2,
            'price' => 350000,
            'subtotal' => 700000,
        ]);
    }
}
