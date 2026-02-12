<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\EmailLog;
use App\Mail\WelcomeMail;
use App\Mail\OrderCreatedMail;
use App\Mail\OrderStatusChangedMail;
use App\Mail\PaymentVerifiedMail;
use App\Mail\OrderInDeliveryMail;
use App\Mail\OrderDeliveredMail;
use App\Mail\PasswordResetMail;
use App\Mail\PasswordChangedMail;
use App\Mail\NewOrderAdminMail;
use App\Mail\LoginNotificationMail;
use App\Mail\ProofRejectedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Servicio centralizado para envío de emails
 * Registra todos los envíos en email_logs para tracking
 */
class EmailService
{
    /**
     * Enviar email de bienvenida al registrarse
     */
    public function sendWelcome(User $user): void
    {
        $this->sendAndLog(
            user: $user,
            emailTo: $user->email,
            emailType: 'welcome',
            subject: '¡Bienvenido a Flores D&D! 🌸',
            mailable: new WelcomeMail($user)
        );
    }

    /**
     * Enviar confirmación de pedido creado
     */
    public function sendOrderCreated(Order $order): void
    {
        $this->sendAndLog(
            user: $order->user,
            emailTo: $order->customer_email,
            emailType: 'order_created',
            subject: "Pedido #{$order->order_number} recibido 🌷",
            mailable: new OrderCreatedMail($order),
            relatedModel: $order
        );

        // Notificar también al admin
        $this->sendNewOrderToAdmin($order);
    }

    /**
     * Enviar notificación de cambio de estado
     */
    public function sendOrderStatusChanged(Order $order, string $previousStatus, string $newStatus): void
    {
        // Mapear estado a tipo de email específico
        $specificEmails = [
            'payment_verified' => fn() => $this->sendPaymentVerified($order),
            'in_delivery' => fn() => $this->sendOrderInDelivery($order),
            'delivered' => fn() => $this->sendOrderDelivered($order),
        ];

        // Si hay un email específico para este estado, usarlo
        if (isset($specificEmails[$newStatus])) {
            $specificEmails[$newStatus]();
            return;
        }

        // Email genérico de cambio de estado
        $this->sendAndLog(
            user: $order->user,
            emailTo: $order->customer_email,
            emailType: 'order_status_changed',
            subject: "Actualización de tu pedido #{$order->order_number}",
            mailable: new OrderStatusChangedMail($order, $previousStatus, $newStatus),
            relatedModel: $order
        );
    }

    /**
     * Enviar confirmación de pago verificado
     */
    public function sendPaymentVerified(Order $order): void
    {
        $this->sendAndLog(
            user: $order->user,
            emailTo: $order->customer_email,
            emailType: 'payment_verified',
            subject: "✅ Pago confirmado - Pedido #{$order->order_number}",
            mailable: new PaymentVerifiedMail($order),
            relatedModel: $order
        );
    }

    /**
     * Enviar notificación de pedido en camino
     */
    public function sendOrderInDelivery(Order $order): void
    {
        $this->sendAndLog(
            user: $order->user,
            emailTo: $order->customer_email,
            emailType: 'order_in_delivery',
            subject: "🚗 ¡Tu pedido está en camino! - #{$order->order_number}",
            mailable: new OrderInDeliveryMail($order),
            relatedModel: $order
        );
    }

    /**
     * Enviar confirmación de pedido entregado
     */
    public function sendOrderDelivered(Order $order): void
    {
        $this->sendAndLog(
            user: $order->user,
            emailTo: $order->customer_email,
            emailType: 'order_delivered',
            subject: "🌸 ¡Pedido entregado! - #{$order->order_number}",
            mailable: new OrderDeliveredMail($order),
            relatedModel: $order
        );
    }

    /**
     * Enviar email de restablecimiento de contraseña
     */
    public function sendPasswordReset(User $user, string $token): void
    {
        $this->sendAndLog(
            user: $user,
            emailTo: $user->email,
            emailType: 'password_reset',
            subject: 'Restablecer contraseña - Flores D&D',
            mailable: new PasswordResetMail($user, $token)
        );
    }

    /**
     * Enviar confirmación de contraseña cambiada
     */
    public function sendPasswordChanged(User $user): void
    {
        $this->sendAndLog(
            user: $user,
            emailTo: $user->email,
            emailType: 'password_changed',
            subject: '🔐 Contraseña actualizada - Flores D&D',
            mailable: new PasswordChangedMail($user)
        );
    }

    /**
     * Notificar al admin de nuevo pedido
     */
    public function sendNewOrderToAdmin(Order $order): void
    {
        $adminEmail = config('flores.admin_email', env('FLORES_ADMIN_EMAIL'));

        if (!$adminEmail) {
            Log::warning('FLORES_ADMIN_EMAIL not configured, skipping admin notification');
            return;
        }

        $this->sendAndLog(
            user: null,
            emailTo: $adminEmail,
            emailType: 'admin_new_order',
            subject: "Nuevo pedido #{$order->order_number}",
            mailable: new NewOrderAdminMail($order),
            relatedModel: $order
        );
    }

    /**
     * Enviar notificación de inicio de sesión
     */
    public function sendLoginNotification(User $user): void
    {
        $this->sendAndLog(
            user: $user,
            emailTo: $user->email,
            emailType: 'login_notification',
            subject: '🔐 Nuevo inicio de sesión - Flores D&D',
            mailable: new LoginNotificationMail(
                $user,
                request()->ip() ?? 'Desconocida',
                substr((string) request()->userAgent(), 0, 150),
                now()->format('d/m/Y H:i')
            )
        );
    }

    /**
     * Enviar notificación de comprobante rechazado
     */
    public function sendProofRejected(Order $order, string $reason): void
    {
        $this->sendAndLog(
            user: $order->user,
            emailTo: $order->customer_email,
            emailType: 'proof_rejected',
            subject: "⚠️ Comprobante rechazado - Pedido #{$order->order_number}",
            mailable: new ProofRejectedMail($order, $reason),
            relatedModel: $order
        );
    }

    /**
     * Método interno para enviar y registrar emails
     */
    private function sendAndLog(
        ?User $user,
        string $emailTo,
        string $emailType,
        string $subject,
        $mailable,
        ?object $relatedModel = null
    ): void {
        // Crear registro de log
        $emailLog = EmailLog::create([
            'user_id' => $user?->id,
            'email_to' => $emailTo,
            'email_type' => $emailType,
            'subject' => $subject,
            'mailable_class' => get_class($mailable),
            'status' => 'queued',
            'related_model_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_model_id' => $relatedModel?->id,
        ]);

        try {
            // Enviar email (usa queue si está configurado)
            Mail::to($emailTo)->queue($mailable);

            // Actualizar log
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Error sending email: {$e->getMessage()}", [
                'email_type' => $emailType,
                'email_to' => $emailTo,
                'exception' => $e,
            ]);

            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
