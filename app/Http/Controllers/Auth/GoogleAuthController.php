<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\AuditService;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController
{
    /**
     * Redirigir al usuario a Google OAuth.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Manejar el callback de Google OAuth.
     * Crea o vincula la cuenta y autentica al usuario.
     */
    public function callback(): RedirectResponse
    {
        // Rate limiting por IP
        $rateKey = 'google-auth:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return redirect()->route('login')
                ->with('error', 'Demasiados intentos. Intenta más tarde.');
        }
        RateLimiter::hit($rateKey, 300);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::warning('Google OAuth callback failed', [
                'ip' => request()->ip(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('error', 'No se pudo autenticar con Google. Intenta nuevamente.');
        }

        // Validar que Google retornó un email
        if (empty($googleUser->getEmail())) {
            return redirect()->route('login')
                ->with('error', 'No se pudo obtener tu email de Google.');
        }

        $user = DB::transaction(function () use ($googleUser) {
            $isNewUser = false;

            // 1. Buscar por google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // 2. Buscar por email (vincular cuenta existente)
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Vincular google_id a cuenta existente
                    $user->google_id = $googleUser->getId();
                    if (!$user->avatar_url && $googleUser->getAvatar()) {
                        $user->avatar_url = $googleUser->getAvatar();
                    }
                    $user->save();

                    AuditService::log('google_account_linked', AuditService::CATEGORY_AUTH, [
                        'user_id' => $user->id,
                        'google_id' => $googleUser->getId(),
                    ], 'User', $user->id);
                } else {
                    // 3. Crear cuenta nueva
                    $user = User::create([
                        'name' => $googleUser->getName() ?? 'Usuario Google',
                        'email' => $googleUser->getEmail(),
                        'password' => Str::random(40),
                        'google_id' => $googleUser->getId(),
                        'avatar_url' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                    ]);

                    $isNewUser = true;

                    AuditService::log('user_registered', AuditService::CATEGORY_AUTH, [
                        'user_id' => $user->id,
                        'method' => 'google',
                    ], 'User', $user->id);
                }
            } else {
                // Actualizar avatar si cambió
                if ($googleUser->getAvatar() && $user->avatar_url !== $googleUser->getAvatar()) {
                    $user->avatar_url = $googleUser->getAvatar();
                    $user->save();
                }
            }

            // Enviar email de bienvenida si es nuevo
            if ($isNewUser) {
                try {
                    app(EmailService::class)->sendWelcome($user);
                } catch (\Exception $e) {
                    Log::warning('Failed to send welcome email for Google user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $user;
        });

        // Verificar si la cuenta está bloqueada
        if ($user->isLocked()) {
            return redirect()->route('login')
                ->with('error', 'Cuenta temporalmente bloqueada. Intenta más tarde.');
        }

        // Autenticar
        Auth::login($user, true);
        request()->session()->regenerate();
        $user->resetFailedLogins();
        AuditService::loginAttempt($user->email, true);

        RateLimiter::clear($rateKey);

        return redirect()->route('account.dashboard');
    }
}
