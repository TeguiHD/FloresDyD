<?php

namespace App\Livewire\Admin;

use App\Models\Popup;
use App\Services\AuditService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
#[Title('Popups y Captación - Flores D&D')]
class Popups extends Component
{
    use WithPagination;
    use WithFileUploads;

    public const AVAILABLE_PAGES = [
        '/'            => 'Inicio',
        '/coleccion'   => 'Colección',
        '/producto/*'  => 'Producto',
        '/carrito'     => 'Carrito',
        '/checkout'    => 'Checkout',
        '/nosotros'    => 'Nosotros',
        '/contacto'    => 'Contacto',
        '/blog'        => 'Blog',
        '/blog/*'      => 'Artículo',
    ];

    public string $filter = 'all';
    public string $search = '';

    public bool $showForm = false;
    public ?int $editingPopupId = null;

    public string $title = '';
    public string $subtitle = '';
    public string $content = '';
    public string $image = '';
    public $imageUpload = null;

    public string $button_text = '';
    public string $button_url = '';

    public string $trigger = 'page_load';
    public string $trigger_value = '';
    public array $selected_pages = [];

    public bool $show_once_per_session = true;
    public bool $show_once_per_user = false;
    public bool $is_active = true;

    public string $starts_at = '';
    public string $expires_at = '';

    public array $filters = [
        'all' => 'Todos',
        'active' => 'Activos',
        'inactive' => 'Inactivos',
        'scheduled' => 'Programados',
        'expired' => 'Expirados',
    ];

    protected $queryString = [
        'filter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function startEdit(int $popupId): void
    {
        $popup = Popup::query()->findOrFail($popupId);

        $this->editingPopupId = $popup->id;
        $this->title = (string) $popup->title;
        $this->subtitle = (string) ($popup->subtitle ?? '');
        $this->content = (string) ($popup->content ?? '');
        $this->image = (string) ($popup->image ?? '');
        $this->button_text = (string) ($popup->button_text ?? '');
        $this->button_url = (string) ($popup->button_url ?? '');
        $this->trigger = (string) $popup->trigger;
        $this->trigger_value = $popup->trigger_value !== null ? (string) $popup->trigger_value : '';
        $this->selected_pages = $popup->show_on_pages ?? [];
        $this->show_once_per_session = (bool) $popup->show_once_per_session;
        $this->show_once_per_user = (bool) $popup->show_once_per_user;
        $this->is_active = (bool) $popup->is_active;
        $this->starts_at = $popup->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->expires_at = $popup->expires_at?->format('Y-m-d\TH:i') ?? '';

        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function clearImageUpload(): void
    {
        $this->imageUpload = null;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'content' => ['nullable', 'string', 'max:1200'],
            'image' => ['nullable', 'string', 'max:255'],
            'imageUpload' => ['nullable', 'image', 'max:5120'],
            'button_text' => ['nullable', 'string', 'max:60'],
            'button_url' => ['nullable', 'string', 'max:2000'],
            'trigger' => ['required', 'in:page_load,exit_intent,scroll,time_delay'],
            'trigger_value' => ['nullable', 'string', 'max:6'],
            'selected_pages' => ['nullable', 'array'],
            'selected_pages.*' => ['string'],
            'show_once_per_session' => ['boolean'],
            'show_once_per_user' => ['boolean'],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date', $this->editingPopupId ? '' : 'after_or_equal:today'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $triggerValue = $this->normalizeTriggerValue($data['trigger'], $data['trigger_value'] ?? '');
        if ($this->triggerRequiresValue($data['trigger']) && $triggerValue === null) {
            $this->addError('trigger_value', 'Este tipo de trigger requiere un valor válido.');
            return;
        }

        if (!empty($data['starts_at']) && !empty($data['expires_at'])) {
            if (strtotime($data['expires_at']) <= strtotime($data['starts_at'])) {
                $this->addError('expires_at', 'La fecha de expiración debe ser posterior al inicio.');
                return;
            }
        }

        $popup = $this->editingPopupId
            ? Popup::query()->findOrFail($this->editingPopupId)
            : new Popup();

        $popup->fill([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?: null,
            'content' => $data['content'] ?: null,
            'image' => $data['image'] ?: null,
            'button_text' => $data['button_text'] ?: null,
            'button_url' => $this->normalizeUrl($data['button_url'] ?? ''),
            'trigger' => $data['trigger'],
            'trigger_value' => $triggerValue,
            'show_on_pages' => !empty($data['selected_pages']) ? array_values($data['selected_pages']) : [],
            'show_once_per_session' => $data['show_once_per_session'],
            'show_once_per_user' => $data['show_once_per_user'],
            'is_active' => $data['is_active'],
            'starts_at' => $data['starts_at'] ?: null,
            'expires_at' => $data['expires_at'] ?: null,
        ]);

        if ($this->imageUpload) {
            $this->deleteStoredFile($popup->image);
            $popup->image = $this->convertAndStoreImage($this->imageUpload);
        }

        $popup->save();

        AuditService::log(
            action: $this->editingPopupId ? 'admin:popup_updated' : 'admin:popup_created',
            category: AuditService::CATEGORY_ADMIN,
            details: [
                'popup_id' => $popup->id,
                'title' => $popup->title,
            ],
            entityType: 'Popup',
            entityId: $popup->id,
        );

        $this->resetForm();
        $this->showForm = false;
    }

    public function deletePopup(int $popupId): void
    {
        $popup = Popup::query()->find($popupId);
        if (!$popup) {
            return;
        }

        $this->deleteStoredFile($popup->image);
        $popup->delete();

        AuditService::log(
            action: 'admin:popup_deleted',
            category: AuditService::CATEGORY_ADMIN,
            details: ['popup_id' => $popupId],
            entityType: 'Popup',
            entityId: $popupId,
        );
    }

    public function render()
    {
        $query = Popup::query()->orderByDesc('created_at');

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('subtitle', 'like', $term)
                    ->orWhere('content', 'like', $term);
            });
        }

        $now = now();
        if ($this->filter === 'active') {
            $query->active();
        } elseif ($this->filter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->filter === 'scheduled') {
            $query->where('is_active', true)
                ->whereNotNull('starts_at')
                ->where('starts_at', '>', $now);
        } elseif ($this->filter === 'expired') {
            $query->whereNotNull('expires_at')
                ->where('expires_at', '<', $now);
        }

        return view('livewire.admin.popups', [
            'popups' => $query->paginate(12),
            'availablePages' => self::AVAILABLE_PAGES,
        ]);
    }

    private function resetForm(): void
    {
        $this->editingPopupId = null;
        $this->title = '';
        $this->subtitle = '';
        $this->content = '';
        $this->image = '';
        $this->imageUpload = null;
        $this->button_text = '';
        $this->button_url = '';
        $this->trigger = 'page_load';
        $this->trigger_value = '';
        $this->selected_pages = [];
        $this->show_once_per_session = true;
        $this->show_once_per_user = false;
        $this->is_active = true;
        $this->starts_at = '';
        $this->expires_at = '';
        $this->resetErrorBag();
    }

    /**
     * Convert uploaded image to WebP and store it.
     */
    private function convertAndStoreImage($upload): string
    {
        $tempPath = $upload->getRealPath();
        $filename = 'popups/' . Str::uuid() . '.webp';
        $storagePath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        $dir = dirname($storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Detect source format and create GD resource
        $mime = mime_content_type($tempPath);
        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($tempPath),
            'image/png' => imagecreatefrompng($tempPath),
            'image/gif' => imagecreatefromgif($tempPath),
            'image/webp' => imagecreatefromwebp($tempPath),
            'image/bmp', 'image/x-ms-bmp' => imagecreatefrombmp($tempPath),
            default => null,
        };

        if ($source === null) {
            // Fallback: store as-is if format is unsupported
            return $upload->store('popups', 'public');
        }

        // Preserve transparency for PNG/GIF sources
        if (in_array($mime, ['image/png', 'image/gif'])) {
            imagepalettetotruecolor($source);
            imagealphablending($source, true);
            imagesavealpha($source, true);
        }

        // Convert to WebP (quality 82 — good balance of size/quality)
        imagewebp($source, $storagePath, 82);
        imagedestroy($source);

        return $filename;
    }

    private function normalizeTriggerValue(string $trigger, ?string $value): ?int
    {
        if (!$this->triggerRequiresValue($trigger)) {
            return null;
        }

        $intValue = (int) preg_replace('/\D+/', '', (string) $value);
        if ($intValue <= 0) {
            return null;
        }

        if ($trigger === 'scroll') {
            return min(100, max(5, $intValue));
        }

        if ($trigger === 'time_delay') {
            return min(3600, max(1, $intValue));
        }

        return $intValue;
    }

    private function triggerRequiresValue(string $trigger): bool
    {
        return in_array($trigger, ['scroll', 'time_delay'], true);
    }

    private function normalizeUrl(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/', '#', 'mailto:', 'tel:'])) {
            return $value;
        }

        return 'https://' . $value;
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
