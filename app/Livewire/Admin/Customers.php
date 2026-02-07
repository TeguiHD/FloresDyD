<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Clientes - Flores D&D')]
class Customers extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    public array $filters = [
        'all' => 'Todos',
        'whitelisted' => 'Whitelist',
        'locked' => 'Bloqueados',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query()->whereDoesntHave('roles');

        if ($this->filter === 'whitelisted') {
            $query->where('is_whitelisted', true);
        }

        if ($this->filter === 'locked') {
            $query->whereNotNull('locked_until')
                ->where('locked_until', '>', now());
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.customers', [
            'customers' => $query->latest()->paginate(12),
            'filters' => $this->filters,
        ]);
    }
}
