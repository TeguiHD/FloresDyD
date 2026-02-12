@component('mail::message')
# ¡Tu Pedido Está en Camino!

Hola **{{ $customerName }}**,

¡Excelentes noticias! Tu pedido **#{{ $order->order_number }}** acaba de salir hacia su destino.

@component('mail::panel')
**Dirección de entrega:**<br>
{{ $deliveryAddress }}

**Horario estimado:** {{ $estimatedTime }}
@endcomponent

## Recomendaciones

- Asegúrate de que alguien pueda recibir el pedido
- Si no hay nadie en casa, intentaremos contactarte
- Las flores se entregan personalmente para garantizar su frescura

@component('mail::button', ['url' => route('track.order.code', $order->tracking_code), 'color' => 'primary'])
Seguir Mi Pedido
@endcomponent

Si necesitas hacer algún cambio de último momento, contáctanos inmediatamente.

Con cariño,<br>
**El equipo de {{ config('app.name') }}**
@endcomponent
