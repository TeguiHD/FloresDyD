<?php

namespace App\Livewire\Admin;

use App\Services\LogReader;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Auditoría - Flores D&D')]
class AuditLogs extends Component
{
    public array $entries = [];

    public function mount(): void
    {
        $this->entries = LogReader::parse(LogReader::tail(storage_path('logs/audit.log'), 160));
    }

    public function render()
    {
        return view('livewire.admin.audit-logs');
    }
}
