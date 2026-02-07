<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\Review;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductoDetalle extends Component
{
    public Product $product;
    public int $quantity = 1;
    public ?string $selectedSize = null;
    public ?string $cardMessage = null;
    public bool $showReviewForm = false;

    public function mount(Product $product): void
    {
        if (!$product->is_active) {
            abort(404);
        }
        
        // Incrementar vistas
        $product->incrementViews();
        
        $this->product = $product;
        
        // Seleccionar tamaño por defecto si hay opciones
        if ($product->sizes && count($product->sizes) > 0) {
            $this->selectedSize = $product->sizes[0]['name'] ?? null;
        }
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < $this->product->stock) {
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
        $this->dispatch('cart:add', [
            'product_id' => $this->product->id,
            'quantity' => $this->quantity,
            'size' => $this->selectedSize,
            'card_message' => $this->cardMessage,
        ]);

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
            ->where('stock', '>', 0)
            ->limit(4)
            ->get();

        $reviews = Review::where('product_id', $this->product->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.pages.producto-detalle', [
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
        ])->title($this->product->name . ' - Flores D&D');
    }
}
