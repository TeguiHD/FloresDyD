<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;
use App\Services\CartService;

/**
 * ProductCard Component - Tarjeta de producto
 * 
 * Implementa:
 * - Análisis en F: Imagen arriba, info esencial abajo
 * - Mapa de calor: Botones en zonas calientes
 * - CTA claro: "Añadir al carrito" prominente
 */
class ProductCard extends Component
{
    public Product $product;
    public bool $showQuickView = true;
    public bool $showAddToCart = true;
    public string $size = 'default'; // default, small, large
    
    public function mount(Product $product, bool $showQuickView = true, bool $showAddToCart = true, string $size = 'default'): void
    {
        $this->product = $product;
        $this->showQuickView = $showQuickView;
        $this->showAddToCart = $showAddToCart;
        $this->size = $size;
    }
    
    public function openQuickView(): void
    {
        $this->dispatch('openQuickView', $this->product->id);
    }
    
    public function addToCart(): void
    {
        // SECURITY: Re-fetch product to ensure it's still active and in stock
        $freshProduct = Product::query()
            ->where('id', $this->product->id)
            ->where('is_active', true)
            ->first();

        if (!$freshProduct) {
            $this->dispatch('showToast', [
                'message' => 'Este producto ya no está disponible',
                'type' => 'error'
            ]);
            return;
        }

        if ($freshProduct->track_stock && $freshProduct->available_stock < 1) {
            $this->dispatch('showToast', [
                'message' => 'Este producto está agotado',
                'type' => 'error'
            ]);
            return;
        }

        $hasVariants = $freshProduct->activeVariants()->exists();

        if ($hasVariants) {
            $this->dispatch('openQuickView', $freshProduct->id);
            return;
        }
        
        $cart = session('cart', []);
        $cart = CartService::addItem($cart, $freshProduct, null, 1, null, null);
        session(['cart' => $cart]);
        
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', [
            'message' => "¡{$freshProduct->name} añadido!",
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.components.product-card');
    }
}
