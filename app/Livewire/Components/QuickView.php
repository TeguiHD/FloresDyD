<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;

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
    
    protected $listeners = [
        'openQuickView' => 'open',
        'closeQuickView' => 'close',
    ];
    
    public function open(int $productId): void
    {
        $this->product = Product::with(['category', 'reviews'])->find($productId);
        
        if ($this->product) {
            $this->isOpen = true;
            $this->quantity = 1;
            $this->selectedImageIndex = 0;
            $this->cardMessage = '';
        }
    }
    
    public function close(): void
    {
        $this->isOpen = false;
        $this->product = null;
    }
    
    public function selectImage(int $index): void
    {
        $this->selectedImageIndex = $index;
    }
    
    public function nextImage(): void
    {
        if ($this->product && count($this->product->gallery) > 0) {
            $this->selectedImageIndex = ($this->selectedImageIndex + 1) % count($this->product->gallery);
        }
    }
    
    public function previousImage(): void
    {
        if ($this->product && count($this->product->gallery) > 0) {
            $total = count($this->product->gallery);
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
        
        if ($this->quantity > $this->product->available_stock) {
            $this->dispatch('showToast', [
                'message' => 'No hay suficiente stock disponible.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Obtener carrito actual
        $cart = session('cart', []);
        $productKey = $this->product->id;
        
        // Si el producto ya está en el carrito, actualizar cantidad
        if (isset($cart[$productKey])) {
            $newQuantity = $cart[$productKey]['quantity'] + $this->quantity;
            
            if ($newQuantity > $this->product->available_stock) {
                $this->dispatch('showToast', [
                    'message' => 'Cantidad máxima alcanzada para este producto.',
                    'type' => 'warning'
                ]);
                return;
            }
            
            $cart[$productKey]['quantity'] = $newQuantity;
        } else {
            // Añadir nuevo producto
            $cart[$productKey] = [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'price' => $this->product->current_price,
                'original_price' => $this->product->price,
                'image' => $this->product->image,
                'quantity' => $this->quantity,
                'card_message' => $this->cardMessage,
            ];
        }
        
        session(['cart' => $cart]);
        
        // Disparar eventos
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', [
            'message' => "¡{$this->product->name} añadido al carrito!",
            'type' => 'success'
        ]);
        
        // Cerrar modal
        $this->close();
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
