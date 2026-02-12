<?php

namespace App\Livewire\Admin;

use App\Models\PromoBanner;
use App\Services\AuditService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Banners Promocionales - Flores D&D')]
class PromoBanners extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingBannerId = null;

    public string $text = '';
    public string $icon = '';
    public string $bg_color = '#1a1a2e';
    public string $text_color = '#ffffff';
    public bool $has_countdown = false;
    public string $starts_at = '';
    public string $ends_at = '';
    public string $button_text = '';
    public string $button_url = '';
    public array $selected_pages = [];
    public bool $is_active = true;
    public bool $is_dismissible = true;

    public array $availablePages = [
        '/' => 'Inicio',
        '/coleccion' => 'Colección',
        '/coleccion/*' => 'Categorías',
        '/producto/*' => 'Productos',
        '/ocasiones' => 'Ocasiones',
        '/servicios' => 'Servicios',
        '/nosotros' => 'Nosotros',
        '/contacto' => 'Contacto',
        '/checkout' => 'Checkout',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function startEdit(int $bannerId): void
    {
        $banner = PromoBanner::query()->findOrFail($bannerId);

        $this->editingBannerId = $banner->id;
        $this->text = (string) $banner->text;
        $this->icon = (string) ($banner->icon ?? '');
        $this->bg_color = (string) ($banner->bg_color ?? '#1a1a2e');
        $this->text_color = (string) ($banner->text_color ?? '#ffffff');
        $this->has_countdown = (bool) $banner->has_countdown;
        $this->starts_at = $banner->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at = $banner->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->button_text = (string) ($banner->button_text ?? '');
        $this->button_url = (string) ($banner->button_url ?? '');
        $this->selected_pages = $banner->show_on_pages ?? [];
        $this->is_active = (bool) $banner->is_active;
        $this->is_dismissible = (bool) $banner->is_dismissible;

        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save(): void
    {
        $data = $this->validate([
            'text' => ['required', 'string', 'max:200'],
            'icon' => ['nullable', 'string', 'max:40'],
            'bg_color' => ['required', 'string', 'max:20', 'regex:/^#[0-9a-fA-F]{3,8}$/'],
            'text_color' => ['required', 'string', 'max:20', 'regex:/^#[0-9a-fA-F]{3,8}$/'],
            'has_countdown' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'button_text' => ['nullable', 'string', 'max:60'],
            'button_url' => ['nullable', 'string', 'max:2000'],
            'selected_pages' => ['array'],
            'selected_pages.*' => ['string', 'max:100'],
            'is_active' => ['boolean'],
            'is_dismissible' => ['boolean'],
        ]);

        if (!empty($data['starts_at']) && !empty($data['ends_at'])) {
            if (strtotime($data['ends_at']) <= strtotime($data['starts_at'])) {
                $this->addError('ends_at', 'La fecha de término debe ser posterior al inicio.');
                return;
            }
        }

        $banner = $this->editingBannerId
            ? PromoBanner::query()->findOrFail($this->editingBannerId)
            : new PromoBanner();

        $showOnPages = array_values(array_filter($data['selected_pages'] ?? []));

        $banner->fill([
            'text' => $data['text'],
            'icon' => $data['icon'] ?: null,
            'bg_color' => $data['bg_color'],
            'text_color' => $data['text_color'],
            'has_countdown' => $data['has_countdown'],
            'starts_at' => $data['starts_at'] ?: null,
            'ends_at' => $data['ends_at'] ?: null,
            'button_text' => $data['button_text'] ?: null,
            'button_url' => $this->normalizeUrl($data['button_url'] ?? ''),
            'show_on_pages' => $showOnPages,
            'is_active' => $data['is_active'],
            'is_dismissible' => $data['is_dismissible'],
            'sort_order' => $this->editingBannerId ? $banner->sort_order : (PromoBanner::query()->max('sort_order') ?? 0) + 1,
        ]);

        $banner->save();

        AuditService::log(
            action: $this->editingBannerId ? 'admin:promo_banner_updated' : 'admin:promo_banner_created',
            category: AuditService::CATEGORY_ADMIN,
            details: ['banner_id' => $banner->id, 'text' => $banner->text],
            entityType: 'PromoBanner',
            entityId: $banner->id,
        );

        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteBanner(int $bannerId): void
    {
        $banner = PromoBanner::query()->find($bannerId);
        if (!$banner) {
            return;
        }

        $banner->delete();

        AuditService::log(
            action: 'admin:promo_banner_deleted',
            category: AuditService::CATEGORY_ADMIN,
            details: ['banner_id' => $bannerId],
            entityType: 'PromoBanner',
            entityId: $bannerId,
        );
    }

    public function render()
    {
        $query = PromoBanner::query()->orderBy('sort_order')->orderByDesc('created_at');

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where('text', 'like', $term)
                ->orWhere('button_text', 'like', $term);
        }

        return view('livewire.admin.promo-banners', [
            'banners' => $query->paginate(12),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingBannerId = null;
        $this->text = '';
        $this->icon = '';
        $this->bg_color = '#1a1a2e';
        $this->text_color = '#ffffff';
        $this->has_countdown = false;
        $this->starts_at = '';
        $this->ends_at = '';
        $this->button_text = '';
        $this->button_url = '';
        $this->selected_pages = [];
        $this->is_active = true;
        $this->is_dismissible = true;
        $this->resetErrorBag();
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
}
