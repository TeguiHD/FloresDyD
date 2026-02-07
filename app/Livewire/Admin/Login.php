<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('Acceso Admin - Flores D&D')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('admin.dashboard');
        }
    }

    public function authenticate(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $rateKey = strtolower($this->email).'|'.request()->ip();

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

        // Reset attempts
        RateLimiter::clear($rateKey);
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();

        // Bootstrap roles if missing
        if (Role::count() === 0) {
            Role::create(['name' => 'super-admin']);
            Role::create(['name' => 'admin']);
            Role::create(['name' => 'moderator']);
            Role::create(['name' => 'user']);
        }

        if (!$user->hasAnyRole(['super-admin', 'admin'])) {
            // First admin bootstrap if no roles assigned
            if ($user->roles()->count() === 0) {
                $user->assignRole('super-admin');
            } else {
                AuditService::loginAttempt($this->email, false);
                throw ValidationException::withMessages([
                    'email' => 'No tienes permisos para acceder al panel.',
                ]);
            }
        }

        Auth::login($user, true);
        AuditService::loginAttempt($this->email, true);

        $this->redirectRoute('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
