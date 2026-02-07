<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email de confirmación de pedido creado
 */
class OrderCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pedido #{$this->order->order_number} recibido 🌷",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.created',
            with: [
                'order' => $this->order,
                'items' => $this->order->items,
                'customerName' => $this->order->customer_name,
                'total' => number_format($this->order->total / 100, 0, ',', '.'),
            ],
        );
    }
}
