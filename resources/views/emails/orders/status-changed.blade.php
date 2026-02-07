@component('mail::message')
# Actualización de tu Pedido

Hola **{{ $customerName }}**,

Tu pedido **#{{ $order->order_number }}** ha sido actualizado.

@component('mail::panel')
**Estado anterior:** {{ $previousStatus }}<br>
**Nuevo estado:** {{ $newStatus }}
@endcomponent

@if($newStatusKey === 'preparing')
## En Preparación

Nuestro equipo está preparando tu arreglo floral con mucho cariño. Usamos solo las flores más frescas para crear algo especial para ti.
@elseif($newStatusKey === 'ready_for_delivery')
## Listo para Envío

Tu pedido está empacado y listo. Pronto saldrá hacia su destino.
@elseif($newStatusKey === 'cancelled')
## Pedido Cancelado

Lamentamos informarte que tu pedido ha sido cancelado.

@if($order->cancellation_reason)
**Motivo:** {{ $order->cancellation_reason }}
@endif

Si tienes dudas sobre esta cancelación, por favor contáctanos.
@endif

@component('mail::button', ['url' => $trackingUrl, 'color' => 'primary'])
Seguir Mi Pedido
@endcomponent

Si tienes alguna pregunta, no dudes en contactarnos.

Con cariño,<br>
**El equipo de {{ config('app.name') }}**
@endcomponent
