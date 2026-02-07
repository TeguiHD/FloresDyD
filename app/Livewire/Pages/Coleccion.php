<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
#[Title('Colección de Flores - Flores D&D')]
class Coleccion extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'newest';

    #[Url]
    public ?int $category = null;

    #[Url]
    public ?float $minPrice = null;

    #[Url]
    public ?float $maxPrice = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'sort', 'category', 'minPrice', 'maxPrice']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0);

        $selectedCategory = $this->category
            ? Category::with('children')->find($this->category)
            : null;

        // Filtro de búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Filtro de categoría
        if ($selectedCategory) {
            if ($selectedCategory->children->count() > 0) {
                $categoryIds = $selectedCategory->children->pluck('id');
                $categoryIds->push($selectedCategory->id);
                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        // Filtro de precio
        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Ordenamiento
        $query = match($this->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            'popular' => $query->orderBy('views', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return view('livewire.pages.coleccion', [
            'products' => $query->paginate(12),
            'categories' => Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get(),
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
