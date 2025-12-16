@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold mb-4">Order Summary</h1>
                <p class="mb-2"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p class="mb-2"><strong>Event:</strong> {{ $order->event->name }}</p>
                <p class="mb-2"><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Customer</h2>
                <p class="mb-2"><strong>Name:</strong> {{ optional($order->customer)->full_name }}</p>
                <p class="mb-2"><strong>Email:</strong> {{ optional($order->customer)->email }}</p>
                <p class="mb-2"><strong>Phone:</strong> {{ optional($order->customer)->phone_number }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Items</h2>
                <div class="space-y-4">
                    @foreach ($order->orderItems as $item)
                        <div class="border rounded p-4 flex items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ $item->ticketType->name }}</p>
                                <p class="text-gray-600">Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</p>
                        </div>
                        @if ($item->tickets->count())
                            <div class="mt-2">
                                <p class="text-sm text-gray-700 font-semibold mb-1">Tickets</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($item->tickets as $ticket)
                                        <div class="border rounded p-3">
                                            <p class="text-sm"><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
                                            <p class="text-sm"><strong>Status:</strong> {{ $ticket->status }}</p>
                                            @if ($ticket->qr_code)
                                                <p class="text-sm"><strong>QR:</strong> {{ $ticket->qr_code }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
