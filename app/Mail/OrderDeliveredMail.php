<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email de pedido entregado
 */
class OrderDeliveredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.aliases.pedidos', 'pedidos@floresdyd.cl'), config('app.name')),
            subject: "🌸 ¡Pedido entregado! - #{$this->order->order_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.delivered',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->customer_name,
                'reviewUrl' => route('orders.review', $this->order->order_number),
            ],
        );
    }
}
