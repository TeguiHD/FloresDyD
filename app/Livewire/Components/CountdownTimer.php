<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Carbon\Carbon;

/**
 * CountdownTimer Component - Cuenta regresiva
 * 
 * Muestra cuenta regresiva para promociones,
 * ofertas con fecha límite, etc.
 */
class CountdownTimer extends Component
{
    public string $targetDate;
    public bool $expired = false;
    public int $days = 0;
    public int $hours = 0;
    public int $minutes = 0;
    public int $seconds = 0;
    public bool $compact = false;
    
    public function mount(string $targetDate, bool $compact = false): void
    {
        $this->targetDate = $targetDate;
        $this->compact = $compact;
        $this->updateCountdown();
    }
    
    public function updateCountdown(): void
    {
        $target = Carbon::parse($this->targetDate);
        $now = Carbon::now();
        
        if ($now->greaterThanOrEqualTo($target)) {
            $this->expired = true;
            $this->days = 0;
            $this->hours = 0;
            $this->minutes = 0;
            $this->seconds = 0;
            return;
        }
        
        $diff = $now->diff($target);
        
        $this->days = $diff->days;
        $this->hours = $diff->h;
        $this->minutes = $diff->i;
        $this->seconds = $diff->s;
    }
    
    public function render()
    {
        return view('livewire.components.countdown-timer');
    }
}
