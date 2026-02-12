@php
    use App\Models\Popup;

    $path = '/' . ltrim(request()->path(), '/');
    $userId = auth()->id();

    $popups = Popup::active()
        ->get()
        ->filter(fn ($popup) => $popup->isVisibleOnPath($path))
        ->map(fn ($popup) => [
            'id' => $popup->id,
            'title' => $popup->title,
            'subtitle' => $popup->subtitle,
            'content' => $popup->content,
            'image' => $popup->image_url,
            'buttonText' => $popup->button_text,
            'buttonUrl' => $popup->button_url,
            'trigger' => $popup->trigger,
            'triggerValue' => $popup->trigger_value,
            'showOncePerSession' => $popup->show_once_per_session,
            'showOncePerUser' => $popup->show_once_per_user,
        ])
        ->values();
@endphp

@if($popups->isNotEmpty())
    <div
        x-data="popupManager({
            popups: @js($popups),
            userId: @js($userId),
            viewEndpoint: @js(url('/api/popups')),
            csrf: @js(csrf_token())
        })"
        x-show="isOpen"
        x-cloak
        class="popup-layer"
        aria-modal="true"
        role="dialog"
    >
        <div class="popup-backdrop" @click="close"></div>
        <div
            class="popup-card"
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            @click.stop
        >
            <button type="button" class="popup-close" @click="close" aria-label="Cerrar popup">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <template x-if="current">
                <div class="popup-grid" :class="!current.image ? 'popup-grid--single' : ''">
                    <div class="popup-media" x-show="current.image">
                        <img :src="current.image" :alt="current.title" loading="lazy">
                    </div>
                    <div class="popup-body">
                        <p class="popup-subtitle" x-text="current.subtitle || 'Edición limitada'"></p>
                        <h3 class="popup-title" x-text="current.title"></h3>
                        <p class="popup-content" x-text="current.content || ''"></p>
                        <div class="popup-actions">
                            <template x-if="current.buttonText && current.buttonUrl">
                                <a
                                    :href="current.buttonUrl"
                                    class="popup-cta"
                                    @click="registerClick(current)"
                                    :data-external="isExternal(current.buttonUrl) ? 'true' : null"
                                    :target="isExternal(current.buttonUrl) ? '_blank' : null"
                                    rel="noopener"
                                >
                                    <span x-text="current.buttonText"></span>
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </template>
                            <button type="button" class="popup-ghost" @click="close">Ahora no</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endif
