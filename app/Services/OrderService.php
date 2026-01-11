<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                $ticketType = TicketType::lockForUpdate()->findOrFail($item['ticket_type_id']);

                // Check availability
                if (!$ticketType->isAvailable($item['quantity'])) {
                    throw new \Exception("Tiket {$ticketType->name} tidak tersedia atau stok tidak mencukupi");
                }

                // Get current price (check war ticket)
                $warTicket = $ticketType->getActiveWarTicket();
                $price = $warTicket ? $warTicket->war_price : $ticketType->price;
                $subtotal = ((int)$price + 4500) * $item['quantity'];

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $ticketType->id,
                    'war_ticket_id' => $warTicket?->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update quota with lock
                if ($warTicket) {
                    $warTicket->decrement('war_available_quota', $item['quantity']);
                } else {
                    $ticketType->decrement('available_quota', $item['quantity']);
                }

                $totalAmount += $subtotal;
            }

            // 4. Update order total
            $order->update(['total_amount' => $totalAmount]);

            // Reload relationships
            $order->load('customer', 'event', 'orderItems.ticketType');

            Log::info('Order created successfully', [
                'order_number' => $order->order_number,
                'total_amount' => $totalAmount,
            ]);

            return $order;
        });
    }

    public function cancelOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Only cancel if status is pending
            if ($order->payment_status !== 'pending') {
                Log::warning('Attempted to cancel non-pending order', [
                    'order_number' => $order->order_number,
                    'current_status' => $order->payment_status,
                ]);
                return $order;
            }

            // Restore quota
            foreach ($order->orderItems as $item) {
                if ($item->war_ticket_id) {
                    $item->warTicket->increment('war_available_quota', $item->quantity);
                    Log::info('Restored war ticket quota', [
                        'war_ticket_id' => $item->war_ticket_id,
                        'quantity' => $item->quantity,
                    ]);
                } else {
                    $item->ticketType->increment('available_quota', $item->quantity);
                    Log::info('Restored ticket quota', [
                        'ticket_type_id' => $item->ticket_type_id,
                        'quantity' => $item->quantity,
                    ]);
                }
            }

            // Update status
            $order->update([
                'payment_status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            Log::info('Order cancelled successfully', [
                'order_number' => $order->order_number,
                'order_status' => $order->payment_status,
            ]);

            return $order;
        });
    }

    public function expireOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Only expire if status is pending
            if ($order->payment_status !== 'pending') {
                return $order;
            }

            // Restore quota
            foreach ($order->orderItems as $item) {
                if ($item->war_ticket_id) {
                    $item->warTicket->increment('war_available_quota', $item->quantity);
                } else {
                    $item->ticketType->increment('available_quota', $item->quantity);
                }
            }

            // Update status
            $order->update([
                'payment_status' => 'expired',
            ]);

            Log::info('Order expired', [
                'order_number' => $order->order_number,
            ]);

            return $order;
        });
    }

    public function markAsPaid(Order $order, string $transactionId)
    {
        $order->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);

        Log::info('Order marked as paid', [
            'order_number' => $order->order_number,
            'transaction_id' => $transactionId,
        ]);

        return $order;
    }
}
