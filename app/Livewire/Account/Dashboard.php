<?php

namespace App\Livewire\Account;

use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Mi cuenta - Flores D&D')]
class Dashboard extends Component
{
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $address = null;

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->redirectRoute('login');
            return;
        }

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone;
        $this->address = $user->address;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'phone' => ['nullable', 'string', 'min:10', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (!$user) {
            $this->redirectRoute('login');
            return;
        }

        $oldName = $user->name;
        $newName = trim($this->name);

        if ($oldName !== $newName) {
            $user->nameHistories()->create([
                'old_name' => $oldName,
                'new_name' => $newName,
                'changed_by' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        }

        $user->name = $newName;
        $user->phone = $this->phone ?: null;
        $user->address = $this->address ?: null;
        $user->save();

        AuditService::log('profile_updated', AuditService::CATEGORY_USER, [
            'user_id' => $user->id,
        ], 'User', $user->id);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Datos actualizados correctamente.',
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $nameHistory = $user
            ? $user->nameHistories()->take(5)->get()
            : collect();

        return view('livewire.account.dashboard', [
            'nameHistory' => $nameHistory,
        ]);
    }
}
