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
 * Email de cambio de estado del pedido
 */
class OrderStatusChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus
    ) {
    }

    public function envelope(): Envelope
    {
        $statusMessages = [
            'pending_review' => 'Comprobante recibido',
            'payment_verified' => '¡Pago verificado!',
            'preparing' => 'Preparando tu pedido',
            'ready_for_delivery' => 'Listo para envío',
            'in_delivery' => '¡En camino! 🚗',
            'delivered' => '¡Pedido entregado!',
            'cancelled' => 'Pedido cancelado',
            'refunded' => 'Pedido reembolsado',
        ];

        $subject = $statusMessages[$this->newStatus] ?? 'Actualización de tu pedido';

        return new Envelope(
            from: new Address(config('mail.aliases.pedidos', 'pedidos@floresdyd.cl'), config('app.name')),
            subject: "Pedido #{$this->order->order_number}: {$subject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.status-changed',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->customer_name,
                'previousStatus' => $this->getStatusLabel($this->previousStatus),
                'newStatus' => $this->getStatusLabel($this->newStatus),
                'newStatusKey' => $this->newStatus,
                'trackingUrl' => route('track.order.code', $this->order->tracking_code),
            ],
        );
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending_payment' => 'Esperando comprobante',
            'pending_review' => 'En revisión',
            'payment_verified' => 'Pago verificado',
            'preparing' => 'En preparación',
            'ready_for_delivery' => 'Listo para envío',
            'in_delivery' => 'En camino',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];

        return $labels[$status] ?? $status;
    }
}
