@component('mail::message')
# ¡Pago Confirmado!

Hola **{{ $customerName }}**,

¡Excelentes noticias! Hemos verificado tu pago para el pedido **#{{ $order->order_number }}**.

@component('mail::panel')
**Monto pagado:** ${{ $total }} CLP<br>
**Verificado el:** {{ now()->format('d/m/Y H:i') }}
@endcomponent

## ¿Qué sigue?

Ahora nuestro equipo comenzará a preparar tu arreglo floral. Te notificaremos cuando esté listo para envío.

@if($deliveryDate)
## Entrega Programada

**Fecha:** {{ $deliveryDate }}<br>
**Horario:** {{ $deliveryTimeSlot ?? 'Durante el día' }}
@endif

@component('mail::button', ['url' => route('orders.show', $order->order_number), 'color' => 'success'])
Ver Estado del Pedido
@endcomponent

¡Gracias por confiar en nosotros!

Con cariño,<br>
**El equipo de {{ config('app.name') }}**
@endcomponent
