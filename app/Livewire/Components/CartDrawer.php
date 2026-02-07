<?php

namespace App\Livewire\Components;

use Livewire\Component;

/**
 * CartDrawer Component - Carrito lateral
 * 
 * Panel deslizable desde la derecha que muestra
 * el contenido del carrito de compras.
 */
class CartDrawer extends Component
{
    public bool $isOpen = false;
    public array $items = [];
    
    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'toggleCart' => 'toggle',
        'openCart' => 'open',
        'closeCart' => 'close',
    ];
    
    public function mount(): void
    {
        $this->refreshCart();
    }
    
    public function refreshCart(): void
    {
        $this->items = session('cart', []);
    }
    
    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->refreshCart();
        }
    }
    
    public function open(): void
    {
        $this->isOpen = true;
        $this->refreshCart();
    }
    
    public function close(): void
    {
        $this->isOpen = false;
    }
    
    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->removeItem($productId);
            return;
        }
        
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session(['cart' => $cart]);
            $this->refreshCart();
            $this->dispatch('cartUpdated');
        }
    }
    
    public function incrementItem(int $productId): void
    {
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
            session(['cart' => $cart]);
            $this->refreshCart();
            $this->dispatch('cartUpdated');
        }
    }
    
    public function decrementItem(int $productId): void
    {
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
                session(['cart' => $cart]);
                $this->refreshCart();
                $this->dispatch('cartUpdated');
            } else {
                $this->removeItem($productId);
            }
        }
    }
    
    public function removeItem(int $productId): void
    {
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            $itemName = $cart[$productId]['name'];
            unset($cart[$productId]);
            session(['cart' => $cart]);
            $this->refreshCart();
            $this->dispatch('cartUpdated');
            $this->dispatch('showToast', [
                'message' => "{$itemName} eliminado del carrito",
                'type' => 'info'
            ]);
        }
    }
    
    public function clearCart(): void
    {
        session()->forget('cart');
        $this->items = [];
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', [
            'message' => 'Carrito vaciado',
            'type' => 'info'
        ]);
    }
    
    public function getSubtotalProperty(): float
    {
        return collect($this->items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }
    
    public function getItemsCountProperty(): int
    {
        return collect($this->items)->sum('quantity');
    }
    
    public function getSavingsProperty(): float
    {
        return collect($this->items)->sum(function ($item) {
            $originalTotal = $item['original_price'] * $item['quantity'];
            $currentTotal = $item['price'] * $item['quantity'];
            return $originalTotal - $currentTotal;
        });
    }
    
    public function proceedToCheckout(): void
    {
        if (empty($this->items)) {
            $this->dispatch('showToast', [
                'message' => 'Tu carrito está vacío',
                'type' => 'warning'
            ]);
            return;
        }
        
        $this->redirect(route('checkout'));
    }
    
    public function render()
    {
        return view('livewire.components.cart-drawer');
    }
}
