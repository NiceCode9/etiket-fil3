<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(Order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer->full_name,
                'email' => $order->customer->email,
                'phone' => $order->customer->phone_number,
            ],
            'item_details' => $this->getItemDetails($order),
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $order->update(['snap_token' => $snapToken]);

            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create snap token: ' . $e->getMessage());
        }
    }

    private function getItemDetails(Order $order)
    {
        $items = [];

        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->ticket_type_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->ticketType->name . ' - ' . $order->event->name,
            ];
        }

        return $items;
    }

    public function handleNotification(array $data)
    {
        $transactionId = $data['transaction_id'] ?? null;
        $orderId = $data['order_id'] ?? null;
        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? null;

        $order = Order::where('order_number', $orderId)->firstOrFail();

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $transactionId,
                    'paid_at' => now(),
                ]);
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $transactionId,
                'paid_at' => now(),
            ]);
        } elseif ($transactionStatus == 'pending') {
            // Do nothing
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $order->update([
                'payment_status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                'transaction_id' => $transactionId,
            ]);
        }

        return $order;
    }
}
