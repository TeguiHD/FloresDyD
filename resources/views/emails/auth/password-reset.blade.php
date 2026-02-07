@component('mail::message')
# Restablecer Contraseña

Hola **{{ $userName }}**,

Recibimos una solicitud para restablecer la contraseña de tu cuenta en Flores D&D.

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Restablecer Contraseña
@endcomponent

Este enlace expirará en **{{ $expiresIn }} minutos**.

## ⚠️ ¿No solicitaste esto?

Si no solicitaste restablecer tu contraseña, puedes ignorar este correo. Tu cuenta permanece segura.

Por seguridad, te recomendamos:
- No compartir este enlace con nadie
- Usar una contraseña única y segura
- Activar la autenticación de dos factores

Si crees que alguien más está intentando acceder a tu cuenta, contáctanos inmediatamente.

Saludos,<br>
**El equipo de {{ config('app.name') }}**

---
<small>Este enlace solo es válido por {{ $expiresIn }} minutos desde que fue solicitado.</small>
@endcomponent
