<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Ocasiones Especiales - Flores D&D')]
class Ocasiones extends Component
{
    public ?string $occasion = null;

    public array $occasions = [
        'cumpleanos' => [
            'title' => 'Cumpleaños',
            'description' => 'Celebra ese día especial con flores frescas y hermosas',
            'icon' => '🎂',
            'color' => 'pink',
        ],
        'aniversario' => [
            'title' => 'Aniversario',
            'description' => 'Renueva tu amor con un detalle floral inolvidable',
            'icon' => '💕',
            'color' => 'red',
        ],
        'san-valentin' => [
            'title' => 'San Valentín',
            'description' => 'Expresa tu amor con rosas y arreglos románticos',
            'icon' => '❤️',
            'color' => 'red',
        ],
        'dia-madres' => [
            'title' => 'Día de las Madres',
            'description' => 'Honra a mamá con las flores más hermosas',
            'icon' => '👩‍👧',
            'color' => 'purple',
        ],
        'graduacion' => [
            'title' => 'Graduación',
            'description' => 'Celebra los logros con un arreglo especial',
            'icon' => '🎓',
            'color' => 'blue',
        ],
        'condolencias' => [
            'title' => 'Condolencias',
            'description' => 'Expresa tu apoyo en momentos difíciles',
            'icon' => '🕊️',
            'color' => 'white',
        ],
        'agradecimiento' => [
            'title' => 'Agradecimiento',
            'description' => 'Di gracias de una manera especial',
            'icon' => '🙏',
            'color' => 'yellow',
        ],
        'recuperacion' => [
            'title' => 'Pronta Recuperación',
            'description' => 'Envía buenos deseos y energía positiva',
            'icon' => '💐',
            'color' => 'green',
        ],
    ];

    public function mount(?string $occasion = null): void
    {
        $this->occasion = $occasion;
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('livewire.pages.ocasiones', [
            'categories' => $categories,
        ]);
    }
}
