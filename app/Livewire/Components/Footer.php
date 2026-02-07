<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\SocialLink;
use App\Models\Category;
use App\Models\SiteSetting;
use App\Models\NewsletterSubscriber;

/**
 * Footer Component - Pie de página
 * 
 * Incluye:
 * - Enlaces de navegación
 * - Información de contacto
 * - Redes sociales
 * - Newsletter (si está habilitado)
 * - Políticas y legales
 */
class Footer extends Component
{
    public string $newsletterEmail = '';
    public bool $newsletterSuccess = false;
    public string $newsletterError = '';
    
    public function subscribeNewsletter(): void
    {
        $this->validate([
            'newsletterEmail' => 'required|email|unique:newsletter_subscribers,email',
        ], [
            'newsletterEmail.required' => 'Por favor ingresa tu email.',
            'newsletterEmail.email' => 'Por favor ingresa un email válido.',
            'newsletterEmail.unique' => 'Este email ya está suscrito.',
        ]);
        
        try {
            NewsletterSubscriber::create([
                'email' => $this->newsletterEmail,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 512),
                'source' => 'footer',
            ]);
            
            $this->newsletterSuccess = true;
            $this->newsletterEmail = '';
            $this->newsletterError = '';
            
        } catch (\Exception $e) {
            $this->newsletterError = 'Hubo un error al suscribirte. Intenta de nuevo.';
        }
    }
    
    public function render()
    {
        $socialLinks = SocialLink::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $contactPhone = SiteSetting::getValue('contact.phone', config('flores.phone'));
        $contactPhoneDisplay = SiteSetting::getValue('contact.phone_display', config('flores.phone_display'));
        $contactEmail = SiteSetting::getValue('contact.email', config('flores.email'));
        $contactAddress = SiteSetting::getValue('contact.address', config('flores.address'));
        $whatsappNumber = SiteSetting::getWhatsappNumber();
        $whatsappDisplay = SiteSetting::getWhatsappDisplay();
            
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
            
        return view('livewire.components.footer', [
            'socialLinks' => $socialLinks,
            'parentCategories' => $parentCategories,
            'contactPhone' => $contactPhone,
            'contactPhoneDisplay' => $contactPhoneDisplay,
            'contactEmail' => $contactEmail,
            'contactAddress' => $contactAddress,
            'whatsappNumber' => $whatsappNumber,
            'whatsappDisplay' => $whatsappDisplay,
        ]);
    }
}
