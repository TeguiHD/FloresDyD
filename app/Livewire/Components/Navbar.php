<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;

/**
 * Navbar Component - Navegación Principal con Mega Menu
 * 
 * Implementa:
 * - Análisis en F: Logo izquierda, navegación principal, CTA derecha
 * - Mega Menu: Categorías padre con subcategorías desplegables
 * - Mobile-first: Menú hamburguesa con acordeón de categorías
 * - Categorías: Bouquets, Regalos, Eventos, Novios, Condolencias
 */
class Navbar extends Component
{
    public bool $mobileMenuOpen = false;
    public bool $searchOpen = false;
    public string $searchQuery = '';
    public int $cartCount = 0;
    
    protected $listeners = [
        'cartUpdated' => 'updateCartCount',
        'closeSearch' => 'closeSearch',
    ];
    
    /**
     * Iconos y colores por slug de categoría padre.
     * Se usa para asignar estilos visuales diferenciados en el mega menu.
     */
    private const CATEGORY_STYLES = [
        'bouquets'      => ['icon' => 'bouquets', 'class' => 'bouquets'],
        'regalos'       => ['icon' => 'regalos', 'class' => 'regalos'],
        'eventos'       => ['icon' => 'eventos', 'class' => 'eventos'],
        'novios'        => ['icon' => 'novios', 'class' => 'novios'],
        'condolencias'  => ['icon' => 'condolencias', 'class' => 'condolencias'],
    ];
    
    public function mount(): void
    {
        $this->cartCount = $this->getCartCount();
    }
    
    public function toggleMobileMenu(): void
    {
        $this->mobileMenuOpen = !$this->mobileMenuOpen;
    }
    
    public function openSearch(): void
    {
        $this->searchOpen = true;
    }
    
    public function closeSearch(): void
    {
        $this->searchOpen = false;
        $this->searchQuery = '';
    }
    
    public function search(): void
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->redirect(route('search', ['q' => $this->searchQuery]));
        }
    }
    
    public function updateCartCount(): void
    {
        $this->cartCount = $this->getCartCount();
    }
    
    private function getCartCount(): int
    {
        if (session()->has('cart')) {
            return collect(session('cart'))->sum('quantity');
        }
        return 0;
    }
    
    /**
     * Obtiene las categorías padre con sus hijos para el mega menu.
     */
    private function getParentCategories()
    {
        return Category::where('is_active', true)
            ->whereNull('parent_id')
            ->where('show_in_navbar', true)
            ->whereIn('slug', array_keys(self::CATEGORY_STYLES))
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
    }
    
    /**
     * Retorna el estilo visual (icono + clase CSS) para una categoría padre.
     */
    public static function getCategoryStyle(string $slug): array
    {
        return self::CATEGORY_STYLES[$slug] ?? ['icon' => 'bouquets', 'class' => 'bouquets'];
    }
    
    public function render()
    {
        $parentCategories = $this->getParentCategories();
        $hasProducts = Product::where('is_active', true)->exists();

        return view('livewire.components.navbar', [
            'parentCategories' => $parentCategories,
            'hasProducts' => $hasProducts,
            'categoryStyles' => self::CATEGORY_STYLES,
        ]);
    }
}
