<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('Usuarios - Flores D&D')]
class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public array $roleOptions = [];
    public ?string $flashMessage = null;

    public bool $showForm = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'admin';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->roleOptions = Role::query()->orderBy('name')->pluck('name')->toArray();
        $this->role = $this->roleOptions[0] ?? 'admin';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function startEdit(int $userId): void
    {
        $user = User::query()->with('roles')->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = (string) ($user->name ?? '');
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->roles->first()?->name ?? ($this->roleOptions[0] ?? 'admin');
        $this->showForm = true;
        $this->flashMessage = null;
        $this->resetErrorBag();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save(): void
    {
        $isEditing = (bool) $this->editingUserId;

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'role' => ['required', Rule::in($this->roleOptions)],
        ];

        if ($isEditing) {
            if ($this->password !== '') {
                $rules['password'] = ['string', 'min:8'];
            }
        } else {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        $data = $this->validate($rules);

        $user = $isEditing ? User::query()->with('roles')->findOrFail($this->editingUserId) : new User();

        if ($user->exists && $user->hasRole('super-admin') && $data['role'] !== 'super-admin') {
            $this->addError('role', 'El rol super-admin no puede ser removido desde el panel.');
            return;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!$user->exists) {
            $user->email_verified_at = now();
        }

        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        AuditService::log(
            action: $isEditing ? 'admin:user_updated' : 'admin:user_created',
            category: AuditService::CATEGORY_ADMIN,
            details: [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $data['role'],
            ],
            entityType: 'User',
            entityId: $user->id,
        );

        $this->flashMessage = $isEditing
            ? "Usuario actualizado: {$user->email}."
            : "Usuario creado: {$user->email}.";

        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteUser(int $userId): void
    {
        $user = User::query()->with('roles')->find($userId);
        if (!$user) {
            return;
        }

        if ($user->hasRole('super-admin')) {
            $this->flashMessage = 'Un usuario super-admin solo puede eliminarse desde la base de datos.';
            return;
        }

        if ($user->id === auth()->id()) {
            $this->flashMessage = 'No puedes eliminar tu propio usuario.';
            return;
        }

        $user->delete();

        AuditService::log(
            action: 'admin:user_deleted',
            category: AuditService::CATEGORY_ADMIN,
            details: [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
            entityType: 'User',
            entityId: $user->id,
        );

        $this->flashMessage = "Usuario eliminado: {$user->email}.";
    }

    private function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = $this->roleOptions[0] ?? 'admin';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = User::query()->with('roles')->whereHas('roles');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.users', [
            'users' => $query->latest()->paginate(12),
            'roleOptions' => $this->roleOptions,
        ]);
    }
}
