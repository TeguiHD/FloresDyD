<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Mail;
use App\Models\SiteSetting;
use App\Models\ContactMessage;

#[Layout('layouts.app')]
#[Title('Contacto - Flores D&D')]
class Contacto extends Component
{
    public string $contactPhone = '';
    public string $contactPhoneDisplay = '';
    public string $contactWhatsapp = '';
    public string $contactWhatsappDisplay = '';
    public string $contactEmail = '';
    public string $contactAddress = '';
    public ?string $mapEmbedUrl = null;
    public array $instagramEmbeds = [];
    public ?string $instagramUsername = null;

    #[Validate('required|min:3|max:100')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:10|max:15')]
    public string $phone = '';

    #[Validate('required|in:general,pedido,evento,otro')]
    public string $subject = 'general';

    #[Validate('required|min:20|max:1000')]
    public string $message = '';

    public bool $submitted = false;

    public function mount(): void
    {
        $this->contactPhone = SiteSetting::getValue('contact.phone', config('flores.phone'));
        $this->contactPhoneDisplay = SiteSetting::getValue('contact.phone_display', config('flores.phone_display'));
        $this->contactWhatsapp = SiteSetting::getWhatsappNumber();
        $this->contactWhatsappDisplay = SiteSetting::getWhatsappDisplay();
        $this->contactEmail = SiteSetting::getValue('contact.email', config('flores.email'));
        $this->contactAddress = SiteSetting::getValue('contact.address', config('flores.address'));
        $this->mapEmbedUrl = SiteSetting::getMapEmbedUrl(config('flores.map_embed_url'));
        $this->instagramEmbeds = SiteSetting::getInstagramEmbeds();
        $this->instagramUsername = SiteSetting::getInstagramUsername();
    }

    public function submit(): void
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'new',
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        // Opcional: enviar notificación por email a administración.
        // Mail::to(config('flores.email'))->send(new ContactForm($this->all()));

        // Por ahora solo mostrar éxito
        $this->submitted = true;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => '¡Mensaje enviado! Te contactaremos pronto.'
        ]);

        // Reset form
        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
    }

    public function render()
    {
        return view('livewire.pages.contacto');
    }
}
