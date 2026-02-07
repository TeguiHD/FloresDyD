<?php

namespace App\Livewire\Components;

use Livewire\Component;

/**
 * Typewriter Component - Efecto máquina de escribir
 * 
 * Muestra texto con efecto de escritura animada.
 * Ideal para heros y secciones destacadas.
 */
class Typewriter extends Component
{
    public array $texts = [];
    public int $currentIndex = 0;
    public string $displayText = '';
    public bool $loop = true;
    public int $typingSpeed = 100;
    public int $pauseDuration = 2000;
    
    public function mount(array $texts, bool $loop = true, int $speed = 100): void
    {
        $this->texts = $texts;
        $this->loop = $loop;
        $this->typingSpeed = $speed;
        $this->displayText = '';
    }
    
    public function render()
    {
        return view('livewire.components.typewriter');
    }
}
