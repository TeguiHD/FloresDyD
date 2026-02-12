<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;
use App\Services\CartService;

/**
 * QuickView Component - Vista rápida de producto
 * 
 * Modal que muestra información esencial del producto
 * sin salir de la página actual.
 * 
 * Implementa:
 * - Galería de imágenes
 * - Selector de cantidad
 * - Añadir al carrito
 * - Información de entrega
 */
class QuickView extends Component
{
    public ?Product $product = null;
    public bool $isOpen = false;
    public int $quantity = 1;
    public int $selectedImageIndex = 0;
    public string $cardMessage = '';
    public ?int $selectedVariantId = null;
    public ?int $customValue = null;
    
    protected $listeners = [
        'openQuickView' => 'open',
        'closeQuickView' => 'close',
    ];
    
    public function open(int $productId): void
    {
        $this->product = Product::with(['category', 'reviews', 'activeVariants'])->find($productId);
        
        if ($this->product) {
            $this->isOpen = true;
            $this->quantity = 1;
            $this->selectedImageIndex = 0;
            $this->cardMessage = '';
            $this->selectedVariantId = null;
            $this->customValue = null;

            $variants = $this->product->activeVariants;
            if ($variants->isNotEmpty()) {
                $first = $variants->first();
                $this->selectedVariantId = $first->id;
                if ($first->type === 'range') {
                    $this->customValue = $first->min_value ?? 1;
                }
            }
        }
    }
    
    public function close(): void
    {
        $this->isOpen = false;
        $this->product = null;
        $this->selectedVariantId = null;
        $this->customValue = null;
    }
    
    public function selectImage(int $index): void
    {
        $this->selectedImageIndex = $index;
    }
    
    public function nextImage(): void
    {
        if ($this->product && count($this->product->image_urls) > 0) {
            $this->selectedImageIndex = ($this->selectedImageIndex + 1) % count($this->product->image_urls);
        }
    }
    
    public function previousImage(): void
    {
        if ($this->product && count($this->product->image_urls) > 0) {
            $total = count($this->product->image_urls);
            $this->selectedImageIndex = ($this->selectedImageIndex - 1 + $total) % $total;
        }
    }
    
    public function incrementQuantity(): void
    {
        if ($this->product && $this->quantity < $this->product->available_stock) {
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
        if (!$this->product) {
            return;
        }

        // SECURITY: Re-verify product is active (may have changed since modal opened)
        $freshProduct = Product::query()
            ->where('id', $this->product->id)
            ->where('is_active', true)
            ->first();

        if (!$freshProduct) {
            $this->dispatch('showToast', [
                'message' => 'Este producto ya no está disponible.',
                'type' => 'error'
            ]);
            $this->close();
            return;
        }

        // SECURITY: Validate quantity against available stock
        $quantity = max(1, (int) $this->quantity);
        if ($freshProduct->track_stock) {
            if ($freshProduct->available_stock < 1) {
                $this->dispatch('showToast', [
                    'message' => 'Este producto está agotado.',
                    'type' => 'error'
                ]);
                return;
            }
            $quantity = min($quantity, $freshProduct->available_stock);
        }

        $variant = null;
        if ($this->selectedVariantId) {
            // SECURITY: Validate variant belongs to this product and is active
            $variant = $freshProduct->activeVariants()->where('id', $this->selectedVariantId)->first();
            if (!$variant) {
                $this->dispatch('showToast', [
                    'message' => 'El formato seleccionado no es válido.',
                    'type' => 'error'
                ]);
                return;
            }

            // SECURITY: Normalize custom value server-side
            if ($variant->type === 'range') {
                $this->customValue = CartService::normalizeCustomValue($variant, $this->customValue);
            } else {
                $this->customValue = null;
            }
        }

        $cart = session('cart', []);
        $cart = CartService::addItem($cart, $freshProduct, $variant, $quantity, $this->cardMessage, $this->customValue);
        session(['cart' => $cart]);
        
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', [
            'message' => "¡{$freshProduct->name} añadido al carrito!",
            'type' => 'success'
        ]);
        
        $this->close();
    }

    public function updatedSelectedVariantId($value): void
    {
        if (!$this->product) {
            $this->customValue = null;
            return;
        }

        $variant = $this->product->activeVariants->firstWhere('id', (int) $value);
        if ($variant && $variant->type === 'range') {
            $this->customValue = $variant->min_value ?? 1;
        } else {
            $this->customValue = null;
        }
    }
    
    public function goToProduct(): void
    {
        if ($this->product) {
            $this->redirect(route('producto', $this->product->slug));
        }
    }
    
    public function render()
    {
        return view('livewire.components.quick-view');
    }
}
