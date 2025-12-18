<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TicketEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $tickets;

    public function __construct(Order $order, array $tickets)
    {
        $this->order = $order;
        $this->tickets = $tickets;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ticket for ' . $this->order->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->tickets as $ticket) {
            // if ($ticket['qr_code_path']) {
            //     $attachments[] = Attachment::fromStorage('public/' . $ticket['qr_code_path'])
            //         ->as("ticket-{$ticket['ticket_number']}.png")
            //         ->withMime('image/png');
            // }

            $ticketObj = is_array($ticket) ? (object) $ticket : $ticket;

            if ($ticketObj->qr_code_path) {
                $fullPath = Storage::disk('public')->path($ticketObj->qr_code_path);

                // Pastikan file exists
                if (file_exists($fullPath)) {
                    $attachments[] = Attachment::fromPath($fullPath)
                        ->as("ticket-{$ticketObj->ticket_number}.png")
                        ->withMime('image/png');
                }
            }
        }

        return $attachments;
    }
}
