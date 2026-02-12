<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\Review;
use App\Services\CartService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductoDetalle extends Component
{
    public Product $product;
    public int $quantity = 1;
    public ?int $selectedVariantId = null;
    public ?int $customValue = null;
    public ?string $cardMessage = null;
    public bool $showReviewForm = false;

    public function mount(Product $product): void
    {
        if (!$product->is_active) {
            abort(404);
        }
        
        // Incrementar vistas
        $product->incrementViews();
        
        $this->product = $product->load('activeVariants');

        $variants = $product->activeVariants()->get();
        if ($variants->isNotEmpty()) {
            $first = $variants->first();
            $this->selectedVariantId = $first->id;
            if ($first->type === 'range') {
                $this->customValue = $first->min_value ?? 1;
            }
        }
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < $this->product->available_stock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        if (!$this->product->is_active) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Este producto no está disponible'
            ]);
            return;
        }

        if ($this->product->track_stock && $this->product->available_stock < 1) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Este producto está agotado'
            ]);
            return;
        }

        $variant = null;
        if ($this->selectedVariantId) {
            // SECURITY: Validar que la variante pertenece al producto y está activa
            $variant = $this->product->activeVariants()
                ->where('id', $this->selectedVariantId)
                ->first();

            if (!$variant) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'El formato seleccionado no es válido'
                ]);
                return;
            }

            // SECURITY: Normalizar customValue server-side para variantes tipo range
            if ($variant->type === 'range') {
                $this->customValue = CartService::normalizeCustomValue($variant, $this->customValue);
            } else {
                $this->customValue = null;
            }
        }

        // SECURITY: Limitar cantidad al stock disponible
        $quantity = max(1, (int) $this->quantity);
        if ($this->product->track_stock) {
            $quantity = min($quantity, $this->product->available_stock);
        }

        $cart = session('cart', []);
        $cart = CartService::addItem($cart, $this->product, $variant, $quantity, $this->cardMessage, $this->customValue);
        session(['cart' => $cart]);

        $this->dispatch('cartUpdated');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => '¡Producto agregado al carrito!'
        ]);
    }

    public function addToWishlist(): void
    {
        $this->dispatch('wishlist:add', [
            'product_id' => $this->product->id,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => '¡Agregado a tu lista de deseos!'
        ]);
    }

    public function render()
    {
        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->inStock()
            ->with('activeVariants')
            ->limit(4)
            ->get();

        $reviews = Review::where('product_id', $this->product->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $metaTitle = $this->product->meta_title ?: ($this->product->name . ' - Flores D&D');
        $metaDescription = $this->product->meta_description
            ?: $this->product->short_description
            ?: Str::limit(strip_tags((string) $this->product->description), 155, '');
        $ogImage = $this->product->main_image_url;

        return view('livewire.pages.producto-detalle', [
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
        ])->title($metaTitle)->layoutData([
            'metaDescription' => $metaDescription,
            'ogImage' => $ogImage,
        ]);
    }

    public function updatedSelectedVariantId($value): void
    {
        // SECURITY: Validate the variant ID belongs to this product
        $variantId = (int) $value;
        $variant = $this->product->activeVariants->firstWhere('id', $variantId);

        if (!$variant) {
            $this->selectedVariantId = $this->product->activeVariants->first()?->id;
            $this->customValue = null;
            return;
        }

        if ($variant->type === 'range') {
            $this->customValue = (int) ($variant->min_value ?? 1);
        } else {
            $this->customValue = null;
        }
    }
}
