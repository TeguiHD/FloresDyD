<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\LogReader;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Seguridad - Flores D&D')]
class Security extends Component
{
    public array $stats = [];
    public array $securityEvents = [];
    public array $loginEvents = [];

    public function mount(): void
    {
        $this->loadSecurity();
    }

    private function loadSecurity(): void
    {
        $lockedUsers = User::query()
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->count();

        $failedAttempts = (int) User::query()->sum('failed_login_attempts');
        $whitelisted = (int) User::query()->where('is_whitelisted', true)->count();
        $avgTrust = (int) round(User::query()->avg('trust_score') ?? 0);

        $securityLog = LogReader::parse(LogReader::tail(storage_path('logs/security.log'), 120));
        $auditLog = LogReader::parse(LogReader::tail(storage_path('logs/audit.log'), 120));

        $this->securityEvents = $securityLog;
        $this->loginEvents = array_values(array_filter($auditLog, function ($entry) {
            return str_contains($entry['message'], 'login_');
        }));

        $honeypotTriggers = collect($securityLog)
            ->filter(fn ($entry) => str_contains($entry['message'], 'Bot/Scanner detectado'))
            ->count();

        $this->stats = [
            ['label' => 'Intentos fallidos', 'value' => $failedAttempts],
            ['label' => 'Cuentas bloqueadas', 'value' => $lockedUsers],
            ['label' => 'Clientes whitelist', 'value' => $whitelisted],
            ['label' => 'Trust score promedio', 'value' => $avgTrust],
            ['label' => 'Honeypots activados', 'value' => $honeypotTriggers],
        ];
    }

    public function render()
    {
        return view('livewire.admin.security');
    }
}
