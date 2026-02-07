<?php

namespace App\Livewire\Pages;

use App\Models\Testimonial;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Sobre Nosotros - Flores D&D')]
class Nosotros extends Component
{
    public array $values = [
        [
            'title' => 'Frescura Garantizada',
            'description' => 'Trabajamos directamente con los mejores proveedores para ofrecer flores de la más alta calidad.',
            'icon' => 'sparkles',
        ],
        [
            'title' => 'Pasión por el Detalle',
            'description' => 'Cada arreglo es creado con dedicación artesanal, cuidando cada flor y cada detalle.',
            'icon' => 'heart',
        ],
        [
            'title' => 'Compromiso Contigo',
            'description' => 'Tu satisfacción es nuestra prioridad. Nos esforzamos por superar tus expectativas.',
            'icon' => 'shield-check',
        ],
        [
            'title' => 'Entrega Puntual',
            'description' => 'Sabemos la importancia de cada momento. Entregamos a tiempo, siempre.',
            'icon' => 'clock',
        ],
    ];

    public array $team = [
        [
            'name' => 'Diana',
            'role' => 'Fundadora & Diseñadora Floral',
            'description' => 'Con más de 15 años de experiencia en diseño floral, Diana lidera cada creación con pasión y creatividad.',
        ],
        [
            'name' => 'David',
            'role' => 'Co-fundador & Operaciones',
            'description' => 'David asegura que cada pedido llegue perfecto y a tiempo, cuidando la logística con precisión.',
        ],
    ];

    public function render()
    {
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('livewire.pages.nosotros', [
            'testimonials' => $testimonials,
        ]);
    }
}
