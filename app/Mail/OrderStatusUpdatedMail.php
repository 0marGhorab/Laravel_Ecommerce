<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus
    ) {
        $this->order->load(['user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order ' . $this->order->order_number . ' – ' . ucfirst($this->order->status),
            from: config('mail.from.address', 'noreply@example.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-updated',
        );
    }
}
