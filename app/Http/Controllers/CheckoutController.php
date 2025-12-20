<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\MidtransService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $orderService;
    protected $midtransService;
    protected $ticketService;

    public function __construct(
        OrderService $orderService,
        MidtransService $midtransService,
        TicketService $ticketService
    ) {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
        $this->ticketService = $ticketService;
    }

    /**
     * Process checkout and create order
     */
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

            // Create order
            $order = $this->orderService->createOrder($validated);

            // Get snap token from Midtrans
            $snapToken = $this->midtransService->createSnapToken($order);

            // Redirect to payment waiting page
            return redirect()->route('payment.waiting', $order->order_number);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show payment waiting page (where Midtrans Snap popup will appear)
     */
    public function paymentWaiting($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Check if order is still pending
        if ($order->payment_status !== 'pending') {
            // If already paid, redirect to success
            if ($order->payment_status === 'paid') {
                return redirect()->route('payment.success', $orderNumber);
            }
            // If failed/cancelled/expired, redirect to failed
            return redirect()->route('payment.failed', $orderNumber);
        }

        // Check if order has expired
        if ($order->expired_at && $order->expired_at->isPast()) {
            $order->update(['payment_status' => 'expired']);
            return redirect()->route('payment.failed', $orderNumber);
        }

        return view('checkout.payment-waiting', compact('order'));
    }

    /**
     * Handle when user cancels/closes Midtrans popup
     */
    public function cancelPayment($orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            // Only cancel if still pending
            if ($order->payment_status === 'pending') {
                $this->orderService->cancelOrder($order);
            }

            return redirect()->route('home')->with('info', 'Pembayaran dibatalkan. Silakan coba lagi.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType', 'orderItems.tickets'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // If not paid yet, redirect back to waiting
        if ($order->payment_status === 'pending') {
            return redirect()->route('payment.waiting', $orderNumber);
        }

        // If failed, redirect to failed page
        if (in_array($order->payment_status, ['failed', 'cancelled', 'expired'])) {
            return redirect()->route('payment.failed', $orderNumber);
        }

        return view('checkout.payment-success', compact('order'));
    }

    /**
     * Payment failed page
     */
    public function paymentFailed($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // If not paid yet, redirect back to waiting
        if ($order->payment_status === 'pending') {
            return redirect()->route('payment.waiting', $orderNumber);
        }

        return view('checkout.payment-failed', compact('order'));
    }

    /**
     * Show order details
     */
    public function show($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType', 'orderItems.tickets'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('order.show', compact('order'));
    }

    /**
     * Check payment status (AJAX endpoint)
     */
    public function checkStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return response()->json([
            'status' => $order->payment_status,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Test email function (for development only)
     */
    public function testEmail(Request $request)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType', 'orderItems.tickets'])
            ->findOrFail($request->id);

        $tickets = $order->orderItems->flatMap(function ($item) {
            return $item->tickets;
        })->toArray();

        $this->ticketService->sendTicketEmail($order, $tickets);

        return response()->json(['success' => true]);
    }
}
