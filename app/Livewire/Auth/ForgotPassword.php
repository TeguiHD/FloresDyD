<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\EmailService;
use App\Services\PasswordService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Recuperar contraseña - Flores D&D')]
class ForgotPassword extends Component
{
    public string $email = '';
    public bool $submitted = false;

    public function send(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $rateKey = 'password:request:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }
        RateLimiter::hit($rateKey, 900);

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $token = app(PasswordService::class)->generateSecureToken();
            $hashed = app(PasswordService::class)->hashToken($token);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $hashed, 'created_at' => now()]
            );

            app(EmailService::class)->sendPasswordReset($user, $token);
        }

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
