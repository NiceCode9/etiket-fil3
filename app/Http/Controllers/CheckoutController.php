<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $orderService;
    protected $midtransService;

    public function __construct(OrderService $orderService, MidtransService $midtransService)
    {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
    }

    public function index(Event $event)
    {
        $event->load('ticketTypes.warTickets');

        return view('checkout.index', compact('event'));
    }

    public function process(Request $request, Event $event)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'identity_type' => 'required|in:ktp,sim,passport,lainnya',
            'identity_number' => 'required|string',
            'items' => 'required|array',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $validated['event_id'] = $event->id;

            $order = $this->orderService->createOrder($validated);
            $snapToken = $this->midtransService->createSnapToken($order);

            return view('checkout.payment', [
                'order' => $order,
                'snapToken' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType', 'orderItems.tickets'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('order.show', compact('order'));
    }
}
