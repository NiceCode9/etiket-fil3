<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    protected $midtransService;
    protected $ticketService;
    protected $invoiceService;

    public function __construct(
        MidtransService $midtransService,
        TicketService $ticketService,
        \App\Services\InvoiceService $invoiceService
    ) {
        $this->midtransService = $midtransService;
        $this->ticketService = $ticketService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle Midtrans notification callback
     */
    public function handle(Request $request)
    {
        try {
            Log::info('Midtrans notification received', $request->all());

            // Handle notification
            $order = $this->midtransService->handleNotification($request->all());

            // If payment successful, generate tickets
            if ($order->payment_status === 'paid') {
                // Check if tickets already generated
                if ($order->orderItems->flatMap->tickets->isEmpty()) {
                    $this->ticketService->generateTicketsForOrder($order);
                    Log::info('Tickets generated for order: ' . $order->order_number);
                }

                // Generate invoice if not exists
                if (!$order->invoice_path) {
                    $this->invoiceService->generateInvoice($order);
                    Log::info('Invoice generated for order: ' . $order->order_number);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification handled successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle finish redirect from Midtrans
     * This is called when user clicks "Selesai" or closes the payment page
     */
    public function finish($orderNumber)
    {
        try {
            // Get latest transaction status from Midtrans
            $status = $this->midtransService->checkTransactionStatus($orderNumber);

            Log::info('Midtrans finish redirect', [
                'order_number' => $orderNumber,
                'status' => $status->transaction_status ?? 'unknown',
            ]);

            // Redirect based on transaction status
            $transactionStatus = $status->transaction_status ?? 'unknown';

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                return redirect()->route('payment.success', $orderNumber);
            } elseif ($transactionStatus === 'pending') {
                return redirect()->route('payment.waiting', $orderNumber)
                    ->with('info', 'Pembayaran Anda sedang diproses. Harap menunggu konfirmasi.');
            } else {
                return redirect()->route('payment.failed', $orderNumber);
            }
        } catch (\Exception $e) {
            Log::error('Midtrans finish redirect error: ' . $e->getMessage());

            // Fallback: redirect to waiting page
            return redirect()->route('payment.waiting', $orderNumber)
                ->with('info', 'Silakan tunggu, kami sedang memverifikasi pembayaran Anda.');
        }
    }
}
