@component('mail::message')
# ¡Bienvenido a Flores D&D!

Hola **{{ $userName }}**,

Gracias por registrarte en nuestra floristería. Estamos encantados de tenerte con nosotros.

@if($verificationUrl)
Para completar tu registro, por favor verifica tu correo electrónico:

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
Verificar Email
@endcomponent
@endif

## ¿Qué puedes hacer ahora?

- Explorar nuestra colección de arreglos florales
- Guardar tus favoritos en tu lista de deseos
- Personalizar tarjetas para tus seres queridos
- Disfrutar de envío gratuito en pedidos seleccionados

Si tienes alguna pregunta, no dudes en contactarnos.

¡Esperamos que encuentres las flores perfectas!

Con cariño,<br>
**El equipo de {{ config('app.name') }}**

---
<small>Este correo fue enviado a {{ $userName }} porque se registró en Flores D&D.</small>
@endcomponent
