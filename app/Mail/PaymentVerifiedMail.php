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
 * Email de confirmación de pago verificado
 */
class PaymentVerifiedMail extends Mailable implements ShouldQueue
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
            subject: "✅ Pago confirmado - Pedido #{$this->order->order_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.payment-verified',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->customer_name,
                'total' => number_format($this->order->total, 0, ',', '.'),
                'deliveryDate' => $this->order->delivery_date ? \Illuminate\Support\Carbon::parse($this->order->delivery_date)->format('d/m/Y') : null,
                'deliveryTimeSlot' => $this->order->delivery_time_slot,
            ],
        );
    }
}
