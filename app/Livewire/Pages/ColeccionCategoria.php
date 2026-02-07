<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ColeccionCategoria extends Component
{
    use WithPagination;

    public Category $category;
    public string $sort = 'newest';

    public function mount(Category $category): void
    {
        if (!$category->is_active) {
            abort(404);
        }
        $this->category = $category;
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()
            ->where('is_active', true)
            ->where('category_id', $this->category->id)
            ->where('stock', '>', 0);

        $query = match($this->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return view('livewire.pages.coleccion-categoria', [
            'products' => $query->paginate(12),
        ])->title($this->category->name . ' - Flores D&D');
    }
}
