<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;

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
        if ($this->product->available_stock < 1) {
            $this->dispatch('showToast', [
                'message' => 'Este producto está agotado',
                'type' => 'error'
            ]);
            return;
        }
        
        $cart = session('cart', []);
        $productKey = $this->product->id;
        
        if (isset($cart[$productKey])) {
            if ($cart[$productKey]['quantity'] < $this->product->available_stock) {
                $cart[$productKey]['quantity']++;
            } else {
                $this->dispatch('showToast', [
                    'message' => 'Cantidad máxima alcanzada',
                    'type' => 'warning'
                ]);
                return;
            }
        } else {
            $cart[$productKey] = [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'price' => $this->product->current_price,
                'original_price' => $this->product->price,
                'image' => $this->product->image,
                'quantity' => 1,
                'card_message' => '',
            ];
        }
        
        session(['cart' => $cart]);
        
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', [
            'message' => "¡{$this->product->name} añadido!",
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.components.product-card');
    }
}
