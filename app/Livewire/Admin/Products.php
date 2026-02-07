<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Productos - Flores D&D')]
class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $filter = 'all';
    public string $search = '';

    public bool $showForm = false;
    public ?int $editingProductId = null;

    public string $name = '';
    public string $slug = '';
    public ?int $category_id = null;
    public string $short_description = '';
    public string $description = '';
    public string $price = '';
    public string $compare_price = '';
    public int $stock = 0;
    public int $low_stock_threshold = 5;
    public bool $track_stock = true;
    public bool $is_active = true;
    public bool $is_featured = false;
    public bool $is_new = false;
    public bool $has_fresh_guarantee = true;
    public bool $has_free_delivery = false;
    public ?string $free_delivery_city = null;
    public bool $has_personalized_card = true;
    public string $custom_badges_input = '';
    public string $main_image = '';
    public string $gallery_images_input = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public array $selectedCoupons = [];
    public $mainImageUpload = null;
    public array $galleryUploads = [];

    public array $filters = [
        'all' => 'Todos',
        'active' => 'Activos',
        'inactive' => 'Inactivos',
        'featured' => 'Destacados',
        'low_stock' => 'Stock bajo',
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

    public function startEdit(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);

        $this->editingProductId = $product->id;
        $this->name = (string) $product->name;
        $this->slug = (string) $product->slug;
        $this->category_id = $product->category_id;
        $this->short_description = (string) ($product->short_description ?? '');
        $this->description = (string) ($product->description ?? '');
        $this->price = (string) ($product->price ?? '');
        $this->compare_price = (string) ($product->compare_price ?? '');
        $this->stock = (int) $product->stock;
        $this->low_stock_threshold = (int) ($product->low_stock_threshold ?? 5);
        $this->track_stock = (bool) $product->track_stock;
        $this->is_active = (bool) $product->is_active;
        $this->is_featured = (bool) $product->is_featured;
        $this->is_new = (bool) $product->is_new;
        $this->has_fresh_guarantee = (bool) $product->has_fresh_guarantee;
        $this->has_free_delivery = (bool) $product->has_free_delivery;
        $this->free_delivery_city = $product->free_delivery_city;
        $this->has_personalized_card = (bool) $product->has_personalized_card;
        $this->custom_badges_input = implode(', ', $product->custom_badges ?? []);
        $this->main_image = (string) ($product->main_image ?? '');
        $this->gallery_images_input = implode(', ', $product->gallery_images ?? []);
        $this->meta_title = (string) ($product->meta_title ?? '');
        $this->meta_description = (string) ($product->meta_description ?? '');
        $this->mainImageUpload = null;
        $this->galleryUploads = [];

        $this->selectedCoupons = $this->getCouponsForProduct($product->id);
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
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($this->editingProductId)],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'short_description' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'string'],
            'compare_price' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'track_stock' => ['boolean'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_new' => ['boolean'],
            'has_fresh_guarantee' => ['boolean'],
            'has_free_delivery' => ['boolean'],
            'free_delivery_city' => ['nullable', 'string', 'max:120'],
            'has_personalized_card' => ['boolean'],
            'custom_badges_input' => ['nullable', 'string'],
            'main_image' => ['nullable', 'string', 'max:255'],
            'gallery_images_input' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'mainImageUpload' => ['nullable', 'image', 'max:5120'],
            'galleryUploads' => ['array'],
            'galleryUploads.*' => ['image', 'max:5120'],
        ]);

        $price = $this->parseMoney($data['price']);
        $comparePrice = $this->parseMoney($data['compare_price'] ?? '');

        $slug = $data['slug'] ?: Str::slug($data['name']);

        $product = $this->editingProductId
            ? Product::query()->findOrFail($this->editingProductId)
            : new Product();

        $product->fill([
            'name' => $data['name'],
            'slug' => $slug,
            'category_id' => $data['category_id'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'price' => $price,
            'compare_price' => $comparePrice > 0 ? $comparePrice : null,
            'discount_percentage' => $comparePrice > 0 && $price > 0
                ? (int) round((1 - ($price / $comparePrice)) * 100)
                : null,
            'stock' => $data['stock'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'track_stock' => $data['track_stock'],
            'is_active' => $data['is_active'],
            'is_featured' => $data['is_featured'],
            'is_new' => $data['is_new'],
            'has_fresh_guarantee' => $data['has_fresh_guarantee'],
            'has_free_delivery' => $data['has_free_delivery'],
            'free_delivery_city' => $data['free_delivery_city'],
            'has_personalized_card' => $data['has_personalized_card'],
            'custom_badges' => $this->splitList($data['custom_badges_input'] ?? ''),
            'main_image' => $data['main_image'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
        ]);

        if ($this->mainImageUpload) {
            $this->deleteStoredFile($product->main_image);
            $product->main_image = $this->mainImageUpload->store('products', 'public');
        }

        $galleryImages = $this->splitList($data['gallery_images_input'] ?? '');
        if (!empty($this->galleryUploads)) {
            foreach ($this->galleryUploads as $upload) {
                $galleryImages[] = $upload->store('products/gallery', 'public');
            }
        }
        $product->gallery_images = array_values(array_unique(array_filter($galleryImages)));

        $product->save();

        $this->syncCoupons($product->id);

        AuditService::log(
            action: $this->editingProductId ? 'admin:product_updated' : 'admin:product_created',
            category: AuditService::CATEGORY_PRODUCT,
            details: [
                'product_id' => $product->id,
                'name' => $product->name,
            ],
            entityType: 'Product',
            entityId: $product->id,
        );

        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteProduct(int $productId): void
    {
        $product = Product::query()->find($productId);
        if (!$product) {
            return;
        }

        $this->deleteStoredFile($product->main_image);
        foreach ($product->gallery_images ?? [] as $image) {
            $this->deleteStoredFile($image);
        }

        $product->delete();
        $this->syncCoupons($productId, true);

        AuditService::log(
            action: 'admin:product_deleted',
            category: AuditService::CATEGORY_PRODUCT,
            details: [
                'product_id' => $productId,
                'name' => $product->name,
            ],
            entityType: 'Product',
            entityId: $productId,
        );
    }

    public function render()
    {
        $query = Product::query()->with('category');

        if ($this->filter === 'active') {
            $query->where('is_active', true);
        }

        if ($this->filter === 'inactive') {
            $query->where('is_active', false);
        }

        if ($this->filter === 'featured') {
            $query->where('is_featured', true);
        }

        if ($this->filter === 'low_stock') {
            $query->where('track_stock', true)
                ->whereRaw('stock - reserved_stock <= low_stock_threshold');
        }

        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.admin.products', [
            'products' => $query->latest()->paginate(12),
            'filters' => $this->filters,
            'categories' => Category::query()->orderBy('name')->get(),
            'coupons' => Coupon::query()->orderBy('code')->get(),
        ]);
    }

    private function parseMoney(?string $value): int
    {
        if (!$value) {
            return 0;
        }
        $normalized = preg_replace('/[^0-9]/', '', $value);
        return (int) $normalized;
    }

    private function splitList(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter(fn ($item) => $item !== '')
            ->values()
            ->toArray();
    }

    public function removeGalleryImage(int $index): void
    {
        $images = $this->splitList($this->gallery_images_input);
        if (!isset($images[$index])) {
            return;
        }
        unset($images[$index]);
        $this->gallery_images_input = implode(', ', array_values($images));
    }

    public function removeGalleryUpload(int $index): void
    {
        if (!isset($this->galleryUploads[$index])) {
            return;
        }

        unset($this->galleryUploads[$index]);
        $this->galleryUploads = array_values($this->galleryUploads);
    }

    public function clearMainImageUpload(): void
    {
        $this->mainImageUpload = null;
    }

    private function getCouponsForProduct(int $productId): array
    {
        return Coupon::query()
            ->get()
            ->filter(function (Coupon $coupon) use ($productId) {
                return in_array($productId, $coupon->applicable_products ?? [], true);
            })
            ->pluck('id')
            ->toArray();
    }

    private function syncCoupons(int $productId, bool $removeOnly = false): void
    {
        $selected = collect($this->selectedCoupons)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $coupons = Coupon::query()->get();
        foreach ($coupons as $coupon) {
            $products = collect($coupon->applicable_products ?? [])->map(fn ($id) => (int) $id);

            if ($removeOnly) {
                $products = $products->reject(fn ($id) => $id === $productId);
            } else {
                if ($selected->contains($coupon->id)) {
                    if (!$products->contains($productId)) {
                        $products->push($productId);
                    }
                } else {
                    $products = $products->reject(fn ($id) => $id === $productId);
                }
            }

            $coupon->applicable_products = $products->unique()->values()->toArray();
            $coupon->save();
        }
    }

    private function resetForm(): void
    {
        $this->editingProductId = null;
        $this->name = '';
        $this->slug = '';
        $this->category_id = null;
        $this->short_description = '';
        $this->description = '';
        $this->price = '';
        $this->compare_price = '';
        $this->stock = 0;
        $this->low_stock_threshold = 5;
        $this->track_stock = true;
        $this->is_active = true;
        $this->is_featured = false;
        $this->is_new = false;
        $this->has_fresh_guarantee = true;
        $this->has_free_delivery = false;
        $this->free_delivery_city = null;
        $this->has_personalized_card = true;
        $this->custom_badges_input = '';
        $this->main_image = '';
        $this->gallery_images_input = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->selectedCoupons = [];
        $this->mainImageUpload = null;
        $this->galleryUploads = [];
        $this->resetErrorBag();
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
