<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Services\AuditService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Mensajes de contacto - Flores D&D')]
class ContactMessages extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public ?int $selectedMessageId = null;
    public string $admin_notes = '';
    public ?string $flashMessage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function selectMessage(int $messageId): void
    {
        $message = ContactMessage::query()->find($messageId);
        if (!$message) {
            return;
        }

        $this->selectedMessageId = $message->id;
        $this->admin_notes = (string) ($message->admin_notes ?? '');
        $this->flashMessage = null;
    }

    public function markHandled(): void
    {
        if (!$this->selectedMessageId) {
            return;
        }

        $message = ContactMessage::query()->find($this->selectedMessageId);
        if (!$message) {
            return;
        }

        $message->status = 'handled';
        $message->handled_by = auth()->id();
        $message->handled_at = now();
        $message->admin_notes = $this->admin_notes ?: null;
        $message->save();

        AuditService::log(
            action: 'admin:contact_message_handled',
            category: AuditService::CATEGORY_ADMIN,
            details: [
                'message_id' => $message->id,
                'email' => $message->email,
            ],
            entityType: 'ContactMessage',
            entityId: $message->id,
        );

        $this->flashMessage = 'Mensaje marcado como gestionado.';
    }

    public function markNew(): void
    {
        if (!$this->selectedMessageId) {
            return;
        }

        $message = ContactMessage::query()->find($this->selectedMessageId);
        if (!$message) {
            return;
        }

        $message->status = 'new';
        $message->handled_by = null;
        $message->handled_at = null;
        $message->admin_notes = $this->admin_notes ?: null;
        $message->save();

        $this->flashMessage = 'Mensaje marcado como nuevo.';
    }

    public function saveNotes(): void
    {
        if (!$this->selectedMessageId) {
            return;
        }

        $message = ContactMessage::query()->find($this->selectedMessageId);
        if (!$message) {
            return;
        }

        $message->admin_notes = $this->admin_notes ?: null;
        $message->save();

        $this->flashMessage = 'Notas guardadas.';
    }

    public function deleteMessage(int $messageId): void
    {
        $message = ContactMessage::query()->find($messageId);
        if (!$message) {
            return;
        }

        $message->delete();

        if ($this->selectedMessageId === $messageId) {
            $this->selectedMessageId = null;
            $this->admin_notes = '';
        }

        AuditService::log(
            action: 'admin:contact_message_deleted',
            category: AuditService::CATEGORY_ADMIN,
            details: [
                'message_id' => $messageId,
                'email' => $message->email,
            ],
            entityType: 'ContactMessage',
            entityId: $messageId,
        );

        $this->flashMessage = 'Mensaje eliminado.';
    }

    public function render()
    {
        $query = ContactMessage::query();

        if ($this->status === 'new') {
            $query->where('status', 'new');
        }

        if ($this->status === 'handled') {
            $query->where('status', 'handled');
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.contact-messages', [
            'messages' => $query->latest()->paginate(12),
        ]);
    }

    public function getSelectedMessageProperty(): ?ContactMessage
    {
        if (!$this->selectedMessageId) {
            return null;
        }

        return ContactMessage::query()->find($this->selectedMessageId);
    }
}
