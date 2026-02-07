@component('mail::message')
# ¡Pedido Entregado!

Hola **{{ $customerName }}**,

¡Tu pedido **#{{ $order->order_number }}** ha sido entregado exitosamente!

Esperamos que las flores lleguen en perfectas condiciones y que iluminen el día de quien las reciba.

## ¿Te gustó tu experiencia?

Tu opinión es muy importante para nosotros. Nos encantaría saber cómo fue tu experiencia.

@component('mail::button', ['url' => $reviewUrl, 'color' => 'primary'])
Dejar una Reseña
@endcomponent

## Consejos para tus flores

- Cambia el agua cada 2 días
- Corta los tallos en diagonal
- Mantén lejos del sol directo y del calor
- Retira las hojas que queden bajo el agua

## ¿Algo no salió bien?

Si tienes algún problema con tu pedido, contáctanos inmediatamente y lo resolveremos.

¡Gracias por elegirnos! Esperamos verte pronto.

Con cariño,<br>
**El equipo de {{ config('app.name') }}**

---
<small>Síguenos en redes sociales para ver nuestras últimas creaciones.</small>
@endcomponent
