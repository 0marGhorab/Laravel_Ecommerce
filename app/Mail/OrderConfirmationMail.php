<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
        $this->order->load(['items', 'shippingAddress', 'billingAddress', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation – ' . $this->order->order_number,
            from: config('mail.from.address', 'noreply@example.com'),
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }
}
