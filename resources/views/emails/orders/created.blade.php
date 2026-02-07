@component('mail::message')
# Pedido Recibido

Hola **{{ $customerName }}**,

¡Gracias por tu pedido! Hemos recibido tu solicitud y la estamos procesando.

## Detalles del Pedido

**Número de pedido:** #{{ $order->order_number }}<br>
**Fecha:** {{ $order->created_at->format('d/m/Y H:i') }}

@component('mail::table')
| Producto | Cantidad | Precio |
|:---------|:--------:|-------:|
@foreach($items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->total_price / 100, 0, ',', '.') }} |
@endforeach
| | **Total:** | **${{ $total }}** |
@endcomponent

## Próximos pasos

1. **Sube tu comprobante de pago** si aún no lo has hecho
2. Verificaremos tu pago en las próximas horas
3. Te notificaremos cuando tu pedido esté en preparación

@component('mail::button', ['url' => route('orders.show', $order->order_number), 'color' => 'primary'])
Ver Mi Pedido
@endcomponent

@if($order->delivery_date)
## Entrega programada
**Fecha:** {{ $order->delivery_date->format('d/m/Y') }}<br>
**Horario:** {{ $order->delivery_time_slot ?? 'Durante el día' }}
@endif

Si tienes alguna pregunta sobre tu pedido, responde a este correo o contáctanos.

Con cariño,<br>
**El equipo de {{ config('app.name') }}**
@endcomponent
