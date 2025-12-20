<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Show tracking page
     */
    public function index()
    {
        return view('tracking.index');
    }

    /**
     * Track order by identity number
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'identity_number' => 'required|string',
        ]);

        // Find customer by identity number
        $customer = Customer::where('identity_number', $validated['identity_number'])->first();

        if (!$customer) {
            return back()->with('error', 'Tidak ada pesanan ditemukan dengan nomor identitas tersebut.');
        }

        // Get all orders for this customer
        $orders = Order::with(['event', 'orderItems.ticketType'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'Tidak ada pesanan ditemukan.');
        }

        return view('tracking.results', compact('orders', 'customer'));
    }

    /**
     * Show order detail from tracking
     */
    public function show($orderNumber)
    {
        $order = Order::with(['customer', 'event', 'orderItems.ticketType'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('tracking.detail', compact('order'));
    }

    /**
     * Download invoice from tracking
     */
    public function downloadInvoice($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('payment_status', 'paid')
            ->firstOrFail();

        $invoiceService = app(\App\Services\InvoiceService::class);

        return $invoiceService->downloadInvoice($order);
    }
}
