<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\EmailService;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Restablecer contraseña - Flores D&D')]
class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $invalid = false;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');

        if ($this->token === '' || $this->email === '') {
            $this->invalid = true;
        }
    }

    public function resetPassword(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:10', 'max:128', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $this->email)->first();
        if (!$record) {
            $this->invalid = true;
            return;
        }

        $expires = (int) (config('auth.passwords.users.expire') ?? 60);
        $createdAt = $record->created_at ? \Carbon\Carbon::parse($record->created_at) : null;
        if (!$createdAt || $createdAt->addMinutes($expires)->isPast()) {
            $this->invalid = true;
            return;
        }

        $hashed = app(PasswordService::class)->hashToken($this->token);
        if (!hash_equals($record->token, $hashed)) {
            $this->invalid = true;
            return;
        }

        $user = User::where('email', $this->email)->first();
        if (!$user) {
            $this->invalid = true;
            return;
        }

        $user->password = $this->password;
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();

        DB::table('password_reset_tokens')->where('email', $this->email)->delete();

        app(EmailService::class)->sendPasswordChanged($user);

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirectRoute('account.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
