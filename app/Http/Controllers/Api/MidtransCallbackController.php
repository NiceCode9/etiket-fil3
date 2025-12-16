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

    public function __construct(MidtransService $midtransService, TicketService $ticketService)
    {
        $this->midtransService = $midtransService;
        $this->ticketService = $ticketService;
    }

    public function handle(Request $request)
    {
        try {
            // Handle notification
            $order = $this->midtransService->handleNotification($request->all());

            // If payment successful, generate tickets
            if ($order->payment_status === 'paid') {
                $this->ticketService->generateTicketsForOrder($order);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification handled',
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
