<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
#[Title('Buscar - Flores D&D')]
class Search extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = collect();
        $suggestedCategories = collect();
        $suggestedProducts = collect();
        $hasProducts = Product::query()->where('is_active', true)->exists();

        $queryText = trim($this->q);

        if (strlen($queryText) >= 2) {
            $terms = preg_split('/\s+/', $queryText);

            $products = Product::query()
                ->where('is_active', true)
                ->inStock()
                ->with('activeVariants')
                ->where(function ($query) use ($queryText, $terms) {
                    $query->where('name', 'like', "%{$queryText}%")
                          ->orWhere('description', 'like', "%{$queryText}%")
                          ->orWhere('short_description', 'like', "%{$queryText}%")
                          ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$queryText]);

                    foreach ($terms as $term) {
                        if (strlen($term) >= 2) {
                            $query->orWhere('name', 'like', "%{$term}%")
                                  ->orWhere('short_description', 'like', "%{$term}%")
                                  ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$term]);
                        }
                    }
                })
                ->orderBy('views_count', 'desc')
                ->paginate(12);

            if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->total() === 0) {
                $suggestedCategories = Category::query()
                    ->where('is_active', true)
                    ->where(function ($query) use ($queryText, $terms) {
                        $query->where('name', 'like', "%{$queryText}%")
                              ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$queryText]);

                        foreach ($terms as $term) {
                            if (strlen($term) >= 2) {
                                $query->orWhere('name', 'like', "%{$term}%")
                                      ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$term]);
                            }
                        }
                    })
                    ->orderBy('sort_order')
                    ->limit(6)
                    ->get();

                if ($suggestedCategories->isNotEmpty()) {
                    $suggestedProducts = Product::query()
                        ->where('is_active', true)
                        ->inStock()
                        ->whereIn('category_id', $suggestedCategories->pluck('id'))
                        ->with('activeVariants')
                        ->orderBy('views_count', 'desc')
                        ->limit(8)
                        ->get();
                }
            }
        }

        return view('livewire.pages.search', [
            'products' => $products,
            'suggestedCategories' => $suggestedCategories,
            'suggestedProducts' => $suggestedProducts,
            'hasProducts' => $hasProducts,
        ]);
    }
}
