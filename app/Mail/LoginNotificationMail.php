<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email de notificación de inicio de sesión
 */
class LoginNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $ipAddress,
        public string $userAgent,
        public string $loginAt
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Nuevo inicio de sesión - Flores D&D',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.login-notification',
            with: [
                'userName' => $this->user->name,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'loginAt' => $this->loginAt,
            ],
        );
    }
}
