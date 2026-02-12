@component('mail::message')
# Nuevo inicio de sesión

Hola **{{ $userName }}**,

Se detectó un nuevo inicio de sesión en tu cuenta de Flores D&D.

## Detalles del acceso:

| Detalle | Información |
|---------|-------------|
| **Fecha y hora** | {{ $loginAt }} |
| **Dirección IP** | {{ $ipAddress }} |
| **Dispositivo** | {{ $userAgent }} |

Si fuiste tú, puedes ignorar este mensaje.

Si **no reconoces** esta actividad, te recomendamos cambiar tu contraseña inmediatamente.

@component('mail::button', ['url' => route('password.request'), 'color' => 'primary'])
Cambiar contraseña
@endcomponent

Con cariño,<br>
**El equipo de {{ config('app.name') }}**

---
<small>Este correo fue enviado como medida de seguridad para proteger tu cuenta.</small>
@endcomponent