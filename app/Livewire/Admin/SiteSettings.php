<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\SiteSetting;
use App\Services\AuditService;

#[Layout('layouts.admin')]
#[Title('Ajustes del Sitio - Flores D&D')]
class SiteSettings extends Component
{
    public string $phone = '';
    public string $phone_display = '';
    public string $whatsapp = '';
    public string $email = '';
    public string $address = '';
    public ?string $map_embed_url = null;

    public bool $saved = false;

    public function mount(): void
    {
        $this->phone = SiteSetting::getValue('contact.phone', config('flores.phone')) ?? '';
        $this->phone_display = SiteSetting::getValue('contact.phone_display', config('flores.phone_display')) ?? '';
        $this->whatsapp = SiteSetting::getValue('contact.whatsapp', config('flores.whatsapp')) ?? '';
        $this->email = SiteSetting::getValue('contact.email', config('flores.email')) ?? '';
        $this->address = SiteSetting::getValue('contact.address', config('flores.address')) ?? '';
        $this->map_embed_url = SiteSetting::getMapEmbedUrl(config('flores.map_embed_url'));
    }

    public function save(): void
    {
        $validated = $this->validate([
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\s-]{7,20}$/'],
            'phone_display' => ['required', 'string', 'max:30'],
            'whatsapp' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\s-]{7,20}$/'],
            'email' => ['required', 'email', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'string', 'max:500'],
        ], [
            'phone.regex' => 'El teléfono contiene caracteres no válidos.',
            'whatsapp.regex' => 'El WhatsApp contiene caracteres no válidos.',
        ]);

        $normalizedWhatsapp = SiteSetting::normalizeWhatsapp($validated['whatsapp']);
        if ($normalizedWhatsapp === '') {
            $this->addError('whatsapp', 'El WhatsApp es inválido. Verifica el número.');
            return;
        }

        if (config('flores.whatsapp_e164_strict')) {
            if (!preg_match('/^\+[1-9]\d{7,14}$/', $validated['whatsapp'])) {
                $this->addError('whatsapp', 'El WhatsApp debe estar en formato E.164 (ej: +56912345678).');
                return;
            }
        }

        $mapUrl = null;
        if (!empty($validated['map_embed_url'])) {
            $mapUrl = SiteSetting::getMapEmbedUrl($validated['map_embed_url']);
            if (!$mapUrl) {
                $this->addError('map_embed_url', 'El enlace debe ser un embed válido de Google Maps.');
                return;
            }
        }

        $changes = [];
        $changes['contact.phone'] = $this->upsertSetting('contact.phone', $validated['phone']);
        $changes['contact.phone_display'] = $this->upsertSetting('contact.phone_display', $validated['phone_display']);
        $changes['contact.whatsapp'] = $this->upsertSetting('contact.whatsapp', $validated['whatsapp']);
        $changes['contact.email'] = $this->upsertSetting('contact.email', $validated['email']);
        $changes['contact.address'] = $this->upsertSetting('contact.address', $validated['address']);
        $changes['contact.map_embed_url'] = $this->upsertSetting('contact.map_embed_url', $mapUrl);

        AuditService::log(
            action: 'admin:site_settings_update',
            category: AuditService::CATEGORY_ADMIN,
            details: ['changes' => $changes],
        );

        $this->saved = true;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Ajustes guardados correctamente.'
        ]);
    }

    private function upsertSetting(string $key, ?string $value): array
    {
        $old = SiteSetting::query()->where('key', $key)->value('value');
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        return ['from' => $old, 'to' => $value];
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }

    public function getWhatsappPreviewProperty(): string
    {
        $normalized = SiteSetting::normalizeWhatsapp($this->whatsapp);
        if ($normalized === '') {
            return '';
        }

        return SiteSetting::formatWhatsappDisplay($normalized);
    }
}
