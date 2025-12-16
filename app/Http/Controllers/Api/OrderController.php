<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\MidtransService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $orderService;
    protected $midtransService;

    public function __construct(OrderService $orderService, MidtransService $midtransService)
    {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'identity_type' => 'required|in:ktp,sim,passport,lainnya',
            'identity_number' => 'required|string',
            'items' => 'required|array',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create order
            $order = $this->orderService->createOrder($request->all());

            // Get snap token from Midtrans
            $snapToken = $this->midtransService->createSnapToken($order);

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order->load('customer', 'event', 'orderItems.ticketType'),
                    'snap_token' => $snapToken,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
}
