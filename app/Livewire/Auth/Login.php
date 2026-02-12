<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuditService;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Iniciar sesión - Flores D&D')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('account.dashboard');
        }
    }

    public function authenticate(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $rateKey = 'login:customer:' . strtolower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            AuditService::loginAttempt($this->email, false);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            RateLimiter::hit($rateKey, 900);
            AuditService::loginAttempt($this->email, false);
            throw ValidationException::withMessages([
                'email' => 'Credenciales inválidas.',
            ]);
        }

        if ($user->isLocked()) {
            AuditService::loginAttempt($this->email, false);
            throw ValidationException::withMessages([
                'email' => 'Cuenta temporalmente bloqueada. Intenta más tarde.',
            ]);
        }

        if (!$user->verifyPassword($this->password)) {
            $user->recordFailedLogin();
            RateLimiter::hit($rateKey, 900);
            AuditService::loginAttempt($this->email, false);
            throw ValidationException::withMessages([
                'email' => 'Credenciales inválidas.',
            ]);
        }

        RateLimiter::clear($rateKey);
        $user->resetFailedLogins();

        if (app(PasswordService::class)->needsRehash($user->password)) {
            $user->password = $this->password;
            $user->save();
        }

        Auth::login($user, $this->remember);
        request()->session()->regenerate();
        AuditService::loginAttempt($this->email, true);

        // Enviar notificación de inicio de sesión
        try {
            app(\App\Services\EmailService::class)->sendLoginNotification($user);
        } catch (\Throwable $e) {
            report($e);
        }

        $this->redirectRoute('account.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
