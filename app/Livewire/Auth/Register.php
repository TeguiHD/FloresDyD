<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuditService;
use App\Services\EmailService;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Crear cuenta - Flores D&D')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $submitted = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('account.dashboard');
        }
    }

    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:10', 'max:128', 'confirmed'],
        ]);

        $rateKey = 'register:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }
        RateLimiter::hit($rateKey, 900);

        $existing = User::where('email', $this->email)->first();
        if ($existing) {
            $this->sendPasswordReset($existing);
            $this->submitted = true;
            return;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        AuditService::log('user_registered', AuditService::CATEGORY_AUTH, [
            'user_id' => $user->id,
        ], 'User', $user->id);

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirectRoute('account.dashboard');
    }

    private function sendPasswordReset(User $user): void
    {
        $token = app(PasswordService::class)->generateSecureToken();
        $hashed = app(PasswordService::class)->hashToken($token);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $hashed, 'created_at' => now()]
        );

        app(EmailService::class)->sendPasswordReset($user, $token);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
