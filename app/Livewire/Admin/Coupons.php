<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CouponService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Marketing & Descuentos - Flores D&D')]
class Coupons extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public string $search = '';

    public bool $showForm = false;
    public ?int $editingCouponId = null;

    public bool $autoCode = true;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public string $type = 'percentage';
    public string $value = '';
    public string $min_purchase_amount = '';
    public string $max_discount_amount = '';
    public string $max_uses = '';
    public string $max_uses_per_user = '1';
    public ?string $starts_at = '';
    public ?string $expires_at = '';
    public ?string $starts_at_date = '';
    public ?string $starts_at_time = '';
    public ?string $expires_at_date = '';
    public ?string $expires_at_time = '';
    public bool $is_active = true;
    public bool $first_purchase_only = false;
    public array $applicable_products = [];
    public array $applicable_categories = [];
    public array $excluded_products = [];

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

    public function startEdit(int $couponId): void
    {
        $coupon = Coupon::query()->findOrFail($couponId);

        $this->editingCouponId = $coupon->id;
        $this->autoCode = false;
        $this->code = (string) $coupon->code;
        $this->name = (string) $coupon->name;
        $this->description = (string) ($coupon->description ?? '');
        $this->type = (string) $coupon->type;
        $this->value = (string) $coupon->value;
        $this->min_purchase_amount = (string) ($coupon->min_purchase_amount ?? '');
        $this->max_discount_amount = (string) ($coupon->max_discount_amount ?? '');
        $this->max_uses = (string) ($coupon->max_uses ?? '');
        $this->max_uses_per_user = (string) ($coupon->max_uses_per_user ?? 1);
        $this->starts_at = $coupon->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->expires_at = $coupon->expires_at?->format('Y-m-d\TH:i') ?? '';
        [$this->starts_at_date, $this->starts_at_time] = $this->splitDateTime($this->starts_at);
        [$this->expires_at_date, $this->expires_at_time] = $this->splitDateTime($this->expires_at);
        $this->is_active = (bool) $coupon->is_active;
        $this->first_purchase_only = (bool) $coupon->first_purchase_only;
        $this->applicable_products = $coupon->applicable_products ?? [];
        $this->applicable_categories = $coupon->applicable_categories ?? [];
        $this->excluded_products = $coupon->excluded_products ?? [];

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
        if (!$this->validateIntegerField($this->max_uses, 'max_uses', 'El tope de usos totales debe ser un número entero.')) {
            return;
        }
        if (!$this->validateIntegerField($this->max_uses_per_user, 'max_uses_per_user', 'El tope por cliente debe ser un número entero.')) {
            return;
        }

        $this->starts_at = $this->combineDateTime($this->starts_at_date, $this->starts_at_time, '00:00');
        $this->expires_at = $this->combineDateTime($this->expires_at_date, $this->expires_at_time, '23:59');

        $data = $this->validate([
            'autoCode' => ['boolean'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($this->editingCouponId)],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'string', 'max:10'],
            'min_purchase_amount' => ['nullable', 'string', 'max:10'],
            'max_discount_amount' => ['nullable', 'string', 'max:10'],
            'max_uses' => ['nullable', 'string', 'max:10'],
            'max_uses_per_user' => ['nullable', 'string', 'max:10'],
            'starts_at' => ['nullable', 'date', $this->editingCouponId ? '' : 'after_or_equal:today'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'],
            'is_active' => ['boolean'],
            'first_purchase_only' => ['boolean'],
            'applicable_products' => ['array'],
            'applicable_products.*' => ['integer', Rule::exists('products', 'id')],
            'applicable_categories' => ['array'],
            'applicable_categories.*' => ['integer', Rule::exists('categories', 'id')],
            'excluded_products' => ['array'],
            'excluded_products.*' => ['integer', Rule::exists('products', 'id')],
        ]);

        $selectedCategories = collect($data['applicable_categories'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();
        $selectedProducts = collect($data['applicable_products'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();
        $excludedProducts = collect($data['excluded_products'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $overlap = $selectedProducts->intersect($excludedProducts);
        if ($overlap->isNotEmpty()) {
            $names = Product::query()
                ->whereIn('id', $overlap)
                ->orderBy('name')
                ->limit(3)
                ->pluck('name')
                ->toArray();
            $label = implode(', ', $names);
            $this->addError('excluded_products', 'Estos productos están seleccionados y excluidos al mismo tiempo: ' . $label);
            return;
        }

        if ($selectedCategories->isNotEmpty() && $selectedProducts->isNotEmpty()) {
            $invalidProducts = Product::query()
                ->whereIn('id', $selectedProducts)
                ->where(function ($query) use ($selectedCategories) {
                    $query->whereNull('category_id')
                        ->orWhereNotIn('category_id', $selectedCategories->all());
                })
                ->orderBy('name')
                ->limit(5)
                ->pluck('name');

            if ($invalidProducts->isNotEmpty()) {
                $this->addError(
                    'applicable_products',
                    'Estos productos no pertenecen a las categorías seleccionadas: ' . $invalidProducts->implode(', ')
                );
                return;
            }
        }

        if ($data['autoCode']) {
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $candidate = CouponService::generateSecureCode();
                $exists = Coupon::query()->where('code', $candidate)->exists();
                if (!$exists) {
                    $code = $candidate;
                    break;
                }
            }
        } else {
            $code = CouponService::sanitizeCode((string) ($data['code'] ?? ''));
        }

        if ($code === '') {
            $this->addError('code', 'El código es inválido.');
            return;
        }

        $value = $this->parseMoney($data['value']);
        if ($value <= 0) {
            $this->addError('value', 'El valor del cupón debe ser mayor a 0.');
            return;
        }

        if ($data['type'] === 'percentage' && $value > 100) {
            $this->addError('value', 'El porcentaje debe estar entre 1 y 100.');
            return;
        }

        $minPurchase = $this->parseMoney($data['min_purchase_amount'] ?? '');
        $maxDiscount = $this->parseMoney($data['max_discount_amount'] ?? '');
        $maxUses = $this->parseInteger($data['max_uses'] ?? '');
        $maxUsesPerUser = max(1, $this->parseInteger($data['max_uses_per_user'] ?? '1'));

        if ($minPurchase > 0 && $maxDiscount > 0 && $data['type'] === 'fixed' && $maxDiscount > $value) {
            $this->addError('max_discount_amount', 'El descuento máximo no puede ser mayor al descuento fijo.');
            return;
        }

        if (!empty($data['starts_at']) && !empty($data['expires_at'])) {
            if (strtotime($data['expires_at']) <= strtotime($data['starts_at'])) {
                $this->addError('expires_at', 'La fecha de expiración debe ser posterior al inicio.');
                return;
            }
        }

        $coupon = $this->editingCouponId
            ? Coupon::query()->findOrFail($this->editingCouponId)
            : new Coupon();

        $coupon->fill([
            'code' => $code,
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type'],
            'value' => $value,
            'min_purchase_amount' => $minPurchase > 0 ? $minPurchase : null,
            'max_discount_amount' => $maxDiscount > 0 ? $maxDiscount : null,
            'max_uses' => $maxUses > 0 ? $maxUses : null,
            'max_uses_per_user' => $maxUsesPerUser,
            'starts_at' => $data['starts_at'] ?: null,
            'expires_at' => $data['expires_at'] ?: null,
            'is_active' => $data['is_active'],
            'first_purchase_only' => $data['first_purchase_only'],
            'applicable_products' => $this->normalizeIds($data['applicable_products'] ?? []),
            'applicable_categories' => $this->normalizeIds($data['applicable_categories'] ?? []),
            'excluded_products' => $this->normalizeIds($data['excluded_products'] ?? []),
            'applicable_users' => [],
        ]);

        $coupon->save();

        AuditService::adminAction(
            $this->editingCouponId ? 'coupon_updated' : 'coupon_created',
            'Coupon',
            $coupon->id,
            [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ]
        );

        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteCoupon(int $couponId): void
    {
        $coupon = Coupon::query()->find($couponId);
        if (!$coupon) {
            return;
        }

        $coupon->delete();

        AuditService::adminAction('coupon_deleted', 'Coupon', $couponId, [
            'code' => $coupon->code,
        ]);
    }

    public function render()
    {
        $query = Coupon::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        $now = now();
        if ($this->filter === 'active') {
            $query->where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                });
        } elseif ($this->filter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->filter === 'scheduled') {
            $query->where('is_active', true)->whereNotNull('starts_at')->where('starts_at', '>', $now);
        } elseif ($this->filter === 'expired') {
            $query->whereNotNull('expires_at')->where('expires_at', '<', $now);
        }

        return view('livewire.admin.coupons', [
            'coupons' => $query->latest()->paginate(12),
            'filters' => $this->filters,
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingCouponId = null;
        $this->autoCode = true;
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->type = 'percentage';
        $this->value = '';
        $this->min_purchase_amount = '';
        $this->max_discount_amount = '';
        $this->max_uses = '';
        $this->max_uses_per_user = '1';
        $this->starts_at = '';
        $this->expires_at = '';
        $this->starts_at_date = '';
        $this->starts_at_time = '';
        $this->expires_at_date = '';
        $this->expires_at_time = '';
        $this->is_active = true;
        $this->first_purchase_only = false;
        $this->applicable_products = [];
        $this->applicable_categories = [];
        $this->excluded_products = [];
        $this->resetErrorBag();
    }

    private function parseMoney(?string $value): int
    {
        if (!$value) {
            return 0;
        }
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    private function parseInteger(?string $value): int
    {
        if (!$value) {
            return 0;
        }
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    private function normalizeIds(array $values): array
    {
        return collect($values)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    private function combineDateTime(?string $date, ?string $time, string $defaultTime): ?string
    {
        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        $time = trim((string) $time);
        if ($time === '') {
            $time = $defaultTime;
        }

        return $date . 'T' . $time;
    }

    private function splitDateTime(?string $value): array
    {
        if (!$value) {
            return ['', ''];
        }

        $parts = explode('T', $value);
        $date = $parts[0] ?? '';
        $time = $parts[1] ?? '';
        if (strlen($time) > 5) {
            $time = substr($time, 0, 5);
        }

        return [$date, $time];
    }

    private function validateIntegerField(?string $value, string $field, string $message): bool
    {
        $value = trim((string) $value);
        if ($value === '') {
            return true;
        }

        if (!preg_match('/^\d+$/', $value)) {
            $this->addError($field, $message);
            return false;
        }

        if ((int) $value < 1) {
            $this->addError($field, 'El valor debe ser al menos 1.');
            return false;
        }

        return true;
    }
}
