@component('mail::message')
# Comprobante rechazado

Hola **{{ $customerName }}**,

Lamentamos informarte que el comprobante de pago que subiste para tu pedido **#{{ $orderNumber }}** fue rechazado.

## Motivo del rechazo:

> {{ $reason }}

Por favor, sube un nuevo comprobante válido lo antes posible para que podamos procesar tu pedido.

@component('mail::button', ['url' => $orderUrl, 'color' => 'primary'])
Ver mi pedido
@endcomponent

Si tienes dudas, puedes contactarnos directamente.

Con cariño,<br>
**El equipo de {{ config('app.name') }}**

---
<small>Este correo fue enviado porque tienes un pedido pendiente en Flores D&D.</small>
@endcomponent