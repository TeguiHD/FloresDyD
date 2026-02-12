<div class="admin-grid lg:grid-cols-3">
    <section class="admin-card lg:col-span-2">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Ajustes generales</p>
                <h1 class="font-display text-2xl">Contactos y ubicación</h1>
            </div>
            <span class="admin-badge">Visible en todo el sitio</span>
        </div>

        <form wire:submit.prevent="save" class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="form-label">Teléfono (para link)</label>
                <input type="text" wire:model.defer="phone" class="form-input" placeholder="56912345678">
                @error('phone') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Teléfono (visual)</label>
                <input type="text" wire:model.defer="phone_display" class="form-input" placeholder="+56 9 1234 5678">
                @error('phone_display') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">WhatsApp</label>
                <input type="text" wire:model.defer="whatsapp" class="form-input" placeholder="56912345678">
                @if(config('flores.whatsapp_e164_strict'))
                    <p class="text-xs text-ink/50 mt-2">Formato requerido: E.164 (ej: +56912345678).</p>
                @else
                    <p class="text-xs text-ink/50 mt-2">Se aceptan números con espacios o guiones. Se normaliza automáticamente.</p>
                @endif
                @if($this->whatsappPreview)
                    <p class="text-xs text-ink/50 mt-2">Vista previa: {{ $this->whatsappPreview }}</p>
                @endif
                @error('whatsapp') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Email</label>
                <input type="email" wire:model.defer="email" class="form-input" placeholder="contacto@floresdyd.com">
                @error('email') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Dirección</label>
                <input type="text" wire:model.defer="address" class="form-input" placeholder="Valdivia y Santiago, Chile">
                @error('address') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Mapa (Google Maps Embed)</label>
                <input type="url" wire:model.defer="map_embed_url" class="form-input" placeholder="https://www.google.com/maps/embed?...">
                <p class="text-xs text-ink/50 mt-2">Acepta enlaces de Google Maps (embed o enlace completo). No uses enlaces acortados como maps.app.goo.gl.</p>
                @error('map_embed_url') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Instagram (usuario)</label>
                <input type="text" wire:model.defer="instagram_username" class="form-input" placeholder="floresdyd">
                <p class="text-xs text-ink/50 mt-2">Sin @. Se usa para enlazar el perfil.</p>
                @error('instagram_username') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Instagram Embeds (publicaciones)</label>
                <textarea wire:model.defer="instagram_embeds" class="form-input min-h-[120px]" placeholder="https://www.instagram.com/p/XXXX/"></textarea>
                <p class="text-xs text-ink/50 mt-2">Una URL por línea. Se aceptan publicaciones o reels.</p>
                @error('instagram_embeds') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex items-center gap-4">
                <button type="submit" class="admin-cta">Guardar cambios</button>
                @if($saved)
                    <span class="text-sm text-emerald-600">Guardado correctamente.</span>
                @endif
            </div>
        </form>
    </section>

    <aside class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">Vista previa</h2>
            <span class="admin-badge">Panel público</span>
        </div>
        <div class="space-y-4 text-sm text-ink/70">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Contacto</p>
                <p class="font-semibold">{{ $phone_display ?: 'Teléfono no definido' }}</p>
                <p>{{ $email ?: 'Email no definido' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">WhatsApp</p>
                <p>{{ $this->whatsappPreview ?: 'Sin WhatsApp válido' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Dirección</p>
                <p>{{ $address ?: 'Dirección no definida' }}</p>
            </div>
            @if($this->mapPreviewUrl)
                <div class="rounded-2xl overflow-hidden border border-[var(--admin-border)]">
                    <iframe src="{{ $this->mapPreviewUrl }}" class="w-full h-40" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            @endif

            @if($instagram_username)
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Instagram</p>
                    <p class="font-semibold">{{ '@' . $instagram_username }}</p>
                </div>
            @endif

            @if($instagram_embeds)
                @php
                    $previewEmbeds = \App\Models\SiteSetting::normalizeInstagramEmbedList($instagram_embeds);
                @endphp
                @if(count($previewEmbeds))
                    <div class="rounded-2xl overflow-hidden border border-[var(--admin-border)]">
                        <iframe src="{{ $previewEmbeds[0] }}" class="w-full h-48" loading="lazy"></iframe>
                    </div>
                @endif
            @endif
        </div>
    </aside>
</div>
