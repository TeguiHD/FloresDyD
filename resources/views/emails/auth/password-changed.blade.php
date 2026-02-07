@component('mail::message')
# Contraseña Actualizada

Hola **{{ $userName }}**,

Te confirmamos que la contraseña de tu cuenta en Flores D&D ha sido actualizada exitosamente.

@component('mail::panel')
**Fecha:** {{ $changedAt }}<br>
**Dirección IP:** {{ $ipAddress }}
@endcomponent

## ¿No fuiste tú?

Si no realizaste este cambio, tu cuenta puede estar comprometida. Por favor:

1. Intenta restablecer tu contraseña inmediatamente
2. Contáctanos a {{ $supportEmail }}
3. Revisa los dispositivos conectados a tu cuenta

@component('mail::button', ['url' => route('password.request'), 'color' => 'red'])
Restablecer Contraseña
@endcomponent

## Consejos de seguridad

- Usa contraseñas únicas para cada cuenta
- Activa la autenticación de dos factores
- No compartas tus credenciales con nadie

Si tienes alguna pregunta, no dudes en contactarnos.

Saludos,<br>
**El equipo de {{ config('app.name') }}**
@endcomponent
