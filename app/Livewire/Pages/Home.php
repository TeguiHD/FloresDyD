<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Testimonial;
use App\Models\PromoBanner;
use App\Models\SiteSetting;

/**
 * Home Page Component
 * 
 * Página principal optimizada para:
 * - Regla de los 8 segundos: Propuesta de valor inmediata
 * - Patrón F: Estructura visual jerárquica
 * - CTAs claros: Acciones definidas en cada sección
 */
class Home extends Component
{
    public function render()
    {
        // Productos destacados (Bestsellers)
        $featuredProducts = Product::with(['category', 'activeVariants'])
            ->active()
            ->where('is_featured', true)
            ->orderByDesc('sales_count')
            ->take(8)
            ->get();
        
        // Productos nuevos
        $newProducts = Product::with(['category', 'activeVariants'])
            ->active()
            ->where('is_new', true)
            ->latest()
            ->take(4)
            ->get();
        
        // Categorías padre para mostrar en home (con subcategorías)
        $categories = Category::whereNull('parent_id')
            ->active()
            ->inHome()
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->ordered()
            ->take(5)
            ->get();
        
        // Testimonios aprobados
        $testimonials = Testimonial::approved()
            ->orderByDesc('rating')
            ->take(6)
            ->get();
        
        // Ofertas activas
        $dealsProducts = Product::with(['category', 'activeVariants'])
            ->active()
            ->whereNotNull('discount_percentage')
            ->where('discount_percentage', '>', 0)
            ->orderByDesc('discount_percentage')
            ->take(4)
            ->get();

        $instagramEmbeds = SiteSetting::getInstagramEmbeds();
        $instagramUsername = SiteSetting::getInstagramUsername();
        
        return view('livewire.pages.home', [
            'featuredProducts' => $featuredProducts,
            'newProducts' => $newProducts,
            'categories' => $categories,
            'testimonials' => $testimonials,
            'dealsProducts' => $dealsProducts,
            'instagramEmbeds' => $instagramEmbeds,
            'instagramUsername' => $instagramUsername,
        ])->layout('layouts.app', [
            'title' => 'Flores D&D | Arreglos Florales Artesanales en Santiago',
            'description' => 'Descubre arreglos florales únicos para cada ocasión especial. Entrega el mismo día en Santiago. ¡Sorprende con flores frescas!',
        ]);
    }
}
