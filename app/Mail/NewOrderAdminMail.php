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
 * Email de nuevo pedido para el administrador
 */
class NewOrderAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        $fraudLevel = match(true) {
            $this->order->fraud_score >= 70 => '🟢',
            $this->order->fraud_score >= 40 => '🟡',
            default => '🔴',
        };

        return new Envelope(
            subject: "{$fraudLevel} Nuevo pedido #{$this->order->order_number} - \${$this->formatTotal()}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-order',
            with: [
                'order' => $this->order,
                'customer' => $this->order->user,
                'items' => $this->order->items,
                'fraudScore' => $this->order->fraud_score,
                'fraudFactors' => $this->order->fraud_factors,
                'adminUrl' => route('admin.orders.show', $this->order),
            ],
        );
    }

    private function formatTotal(): string
    {
        return number_format($this->order->total / 100, 0, ',', '.');
    }
}
