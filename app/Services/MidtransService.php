<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

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
            'callbacks' => [
                'finish' => route('payment.finish', $order->order_number),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'snap_token' => $snapToken,
                'expired_at' => now()->addHours(24),
            ]);

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
        try {
            // Create notification instance
            $notification = new Notification($data);

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $orderId = $notification->order_id;
            $transactionId = $notification->transaction_id;

            $order = Order::with(['orderItems.tickets'])
                ->where('order_number', $orderId)
                ->firstOrFail();

            // Handle different transaction statuses
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->setOrderPaid($order, $transactionId);
                } else {
                    $order->update([
                        'payment_status' => 'pending',
                        'transaction_id' => $transactionId,
                    ]);
                }
            } elseif ($transactionStatus == 'settlement') {
                $this->setOrderPaid($order, $transactionId);
            } elseif ($transactionStatus == 'pending') {
                $order->update([
                    'payment_status' => 'pending',
                    'transaction_id' => $transactionId,
                ]);
            } elseif ($transactionStatus == 'deny') {
                $order->update([
                    'payment_status' => 'failed',
                    'transaction_id' => $transactionId,
                ]);
            } elseif ($transactionStatus == 'expire') {
                $order->update([
                    'payment_status' => 'expired',
                    'transaction_id' => $transactionId,
                ]);
            } elseif ($transactionStatus == 'cancel') {
                $order->update([
                    'payment_status' => 'cancelled',
                    'transaction_id' => $transactionId,
                ]);
            }

            return $order;
        } catch (\Exception $e) {
            throw new \Exception('Failed to handle notification: ' . $e->getMessage());
        }
    }

    /**
     * Set order as paid and trigger ticket generation
     */
    private function setOrderPaid(Order $order, $transactionId)
    {
        $order->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);

        return $order;
    }

    /**
     * Check transaction status from Midtrans
     */
    public function checkTransactionStatus($orderNumber)
    {
        try {
            $status = \Midtrans\Transaction::status($orderNumber);
            return $status;
        } catch (\Exception $e) {
            throw new \Exception('Failed to check transaction status: ' . $e->getMessage());
        }
    }
}
