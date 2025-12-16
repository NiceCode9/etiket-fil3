<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Your Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: #f9f9f9;
        }

        .ticket-info {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #4F46E5;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Your Ticket is Ready!</h1>
        </div>

        <div class="content">
            <p>Hello {{ $order->customer->full_name }},</p>

            <p>Thank you for your purchase! Your ticket for <strong>{{ $order->event->name }}</strong> is now ready.</p>

            <div class="ticket-info">
                <h3>Event Details</h3>
                <p><strong>Event:</strong> {{ $order->event->name }}</p>
                <p><strong>Venue:</strong> {{ $order->event->venue }}</p>
                <p><strong>Date:</strong> {{ $order->event->event_date->format('d F Y, H:i') }}</p>
            </div>

            <div class="ticket-info">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Total Tickets:</strong> {{ count($tickets) }}</p>
                <p><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>

            <p><strong>Important:</strong> Please bring your QR code (attached) to the venue. You will need to scan it
                to get your wristband.</p>

            <p>Your QR codes are attached to this email. Please save them on your phone or print them.</p>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact our support.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
