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
 * Email de confirmación de cambio de contraseña
 */
class PasswordChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Contraseña actualizada - Flores D&D',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.password-changed',
            with: [
                'userName' => $this->user->name,
                'changedAt' => now()->format('d/m/Y H:i'),
                'ipAddress' => request()->ip(),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }
}
