<?php

namespace App\Livewire\Admin;

use App\Services\AuditService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('Roles y permisos - Flores D&D')]
class AccessControl extends Component
{
    public array $roles = [];
    public array $permissions = [];
    public ?int $activeRoleId = null;
    public array $selectedPermissions = [];
    public bool $saved = false;

    public function mount(): void
    {
        $this->roles = Role::query()
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ])
            ->toArray();

        $this->permissions = Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $defaultRole = Role::query()->where('name', 'admin')->first() ?? Role::query()->first();
        if ($defaultRole) {
            $this->setActiveRole($defaultRole->id);
        }
    }

    public function setActiveRole(int $roleId): void
    {
        $role = Role::query()->with('permissions')->find($roleId);
        if (!$role) {
            return;
        }

        $this->activeRoleId = $role->id;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->saved = false;
    }

    public function save(): void
    {
        if (!$this->activeRoleId) {
            return;
        }

        $role = Role::query()->find($this->activeRoleId);
        if (!$role) {
            return;
        }

        if ($role->name === 'super-admin') {
            $this->saved = false;
            return;
        }

        $validPermissions = array_values(array_intersect($this->selectedPermissions, $this->permissions));
        $role->syncPermissions($validPermissions);

        AuditService::log(
            action: 'role:permissions_update',
            category: AuditService::CATEGORY_ADMIN,
            details: [
                'role' => $role->name,
                'permissions' => $validPermissions,
            ],
            entityType: 'Role',
            entityId: $role->id,
        );

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.admin.access-control');
    }
}
