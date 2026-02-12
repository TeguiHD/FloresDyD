@component('mail::message')
# Nuevo Pedido

**Riesgo:** {{ $fraudScore >= 70 ? 'Bajo' : ($fraudScore >= 40 ? 'Medio' : 'Alto') }}

**Pedido:** #{{ $order->order_number }}<br>
**Fecha:** {{ $order->created_at->format('d/m/Y H:i') }}

---

## Análisis de Riesgo

@component('mail::panel')
**Fraud Score:** {{ $fraudScore }}/100

@if($fraudScore >= 70)
**BAJO RIESGO** - Puede aprobarse automáticamente
@elseif($fraudScore >= 40)
**RIESGO MEDIO** - Requiere revisión rápida
@else
**ALTO RIESGO** - Requiere revisión exhaustiva
@endif
@endcomponent

@if($fraudFactors)
### Factores detectados:
@foreach($fraudFactors as $factor => $value)
- **{{ $factor }}:** {{ $value }}
@endforeach
@endif

---

## Cliente

**Nombre:** {{ $customer->name }}<br>
**Email:** {{ $customer->email }}<br>
**Trust Score:** {{ $customer->trust_score }}/100<br>
**Compras exitosas:** {{ $customer->successful_orders }}<br>
**Whitelisted:** {{ $customer->is_whitelisted ? 'Sí' : 'No' }}

---

## Productos

@component('mail::table')
| Producto | Cant. | Precio |
|:---------|:-----:|-------:|
@foreach($items as $item)
| {{ $item->product_name }}@if($item->variant_label) ({{ $item->variant_label }})@endif | {{ $item->quantity }} | ${{ number_format($item->total_price, 0, ',', '.') }} |
@endforeach
| | **Total:** | **${{ number_format($order->total, 0, ',', '.') }}** |
@endcomponent

---

## Entrega

**Fecha:** {{ $order->delivery_date?->format('d/m/Y') ?? 'No especificada' }}<br>
**Horario:** {{ $order->delivery_time_slot ?? 'Durante el día' }}

@component('mail::button', ['url' => $adminUrl, 'color' => 'primary'])
Ver en Panel Admin
@endcomponent

---
<small>Este es un correo automático del sistema de Flores D&D</small>
@endcomponent
