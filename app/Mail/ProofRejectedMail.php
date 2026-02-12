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
 * Email de notificación de rechazo de comprobante de pago
 */
class ProofRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $reason
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.aliases.pedidos', 'pedidos@floresdyd.cl'), config('app.name')),
            subject: "⚠️ Comprobante rechazado - Pedido #{$this->order->order_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.proof-rejected',
            with: [
                'orderNumber' => $this->order->order_number,
                'customerName' => $this->order->customer_name,
                'reason' => $this->reason,
                'orderUrl' => route('account.orders.show', $this->order->id),
            ],
        );
    }
}
