<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create or get customer
            $customer = Customer::firstOrCreate(
                ['email' => $data['email']],
                [
                    'full_name' => $data['full_name'],
                    'phone_number' => $data['phone_number'],
                    'identity_type' => $data['identity_type'],
                    'identity_number' => $data['identity_number'],
                ]
            );

            // 2. Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'event_id' => $data['event_id'],
                'total_amount' => 0, // Will calculate below
                'payment_status' => 'pending',
            ]);

            $totalAmount = 0;

            // 3. Create order items
            foreach ($data['items'] as $item) {
                $ticketType = TicketType::findOrFail($item['ticket_type_id']);

                // Check availability
                if (!$ticketType->isAvailable($item['quantity'])) {
                    throw new \Exception("Ticket {$ticketType->name} not available");
                }

                // Get current price (check war ticket)
                $warTicket = $ticketType->getActiveWarTicket();
                $price = $warTicket ? $warTicket->war_price : $ticketType->price;
                $subtotal = $price * $item['quantity'];

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $ticketType->id,
                    'war_ticket_id' => $warTicket?->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update quota
                if ($warTicket) {
                    $warTicket->decrement('war_available_quota', $item['quantity']);
                } else {
                    $ticketType->decrement('available_quota', $item['quantity']);
                }

                $totalAmount += $subtotal;
            }

            // 4. Update order total
            $order->update(['total_amount' => $totalAmount]);

            return $order;
        });
    }

    public function cancelOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Restore quota
            foreach ($order->orderItems as $item) {
                if ($item->war_ticket_id) {
                    $item->warTicket->increment('war_available_quota', $item->quantity);
                } else {
                    $item->ticketType->increment('available_quota', $item->quantity);
                }
            }

            // Update status
            $order->update(['payment_status' => 'cancelled']);

            return $order;
        });
    }
}
