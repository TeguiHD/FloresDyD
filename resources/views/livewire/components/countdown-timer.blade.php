{{-- 
    CountdownTimer - Cuenta regresiva para promociones
    Usa Alpine.js para actualización en tiempo real
--}}
<div 
    x-data="countdown('{{ $targetDate }}')"
    x-init="$nextTick(() => { if (typeof countdown !== 'undefined') { $el.dataset.initialized = true; } })"
    wire:poll.1s="updateCountdown"
    @class([
        'countdown inline-flex',
        'text-xs' => $compact,
        'text-sm' => !$compact,
    ])
>
    @if($expired)
        <span class="text-red-500 font-medium">¡Oferta terminada!</span>
    @else
        @if($days > 0)
            <div class="countdown__item {{ $compact ? 'px-1.5 py-0.5' : 'px-2 py-1' }}">
                <span class="countdown__number">{{ str_pad($days, 2, '0', STR_PAD_LEFT) }}</span>
                @unless($compact)
                    <span class="countdown__label block">días</span>
                @else
                    <span class="countdown__label">d</span>
                @endunless
            </div>
            <span class="self-center mx-0.5">:</span>
        @endif
        
        <div class="countdown__item {{ $compact ? 'px-1.5 py-0.5' : 'px-2 py-1' }}">
            <span class="countdown__number">{{ str_pad($hours, 2, '0', STR_PAD_LEFT) }}</span>
            @unless($compact)
                <span class="countdown__label block">hrs</span>
            @else
                <span class="countdown__label">h</span>
            @endunless
        </div>
        <span class="self-center mx-0.5">:</span>
        
        <div class="countdown__item {{ $compact ? 'px-1.5 py-0.5' : 'px-2 py-1' }}">
            <span class="countdown__number">{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }}</span>
            @unless($compact)
                <span class="countdown__label block">min</span>
            @else
                <span class="countdown__label">m</span>
            @endunless
        </div>
        <span class="self-center mx-0.5">:</span>
        
        <div class="countdown__item {{ $compact ? 'px-1.5 py-0.5' : 'px-2 py-1' }}">
            <span class="countdown__number">{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</span>
            @unless($compact)
                <span class="countdown__label block">seg</span>
            @else
                <span class="countdown__label">s</span>
            @endunless
        </div>
    @endif
</div>
