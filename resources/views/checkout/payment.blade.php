@extends('layouts.app')

@section('title', 'Payment')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Complete Payment</h1>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>

            <button id="pay-button"
                class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                Pay Now
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = '{{ route('order.show', $order->order_number) }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route('order.show', $order->order_number) }}';
                },
                onError: function(result) {
                    alert('Payment failed');
                },
                onClose: function() {
                    alert('You closed the popup without finishing the payment');
                }
            });
        });
    </script>
@endpush
