@extends('layouts.app')

@section('content')
<div class="min-h-dvh bg-white">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto prose prose-lg">
            <h1 class="font-sans font-bold text-3xl text-primary">Términos y Condiciones</h1>
            
            <p class="text-dark/70">Última actualización: {{ now()->format('d/m/Y') }}</p>

            <h2>1. Aceptación de los términos</h2>
            <p>
                Al acceder y utilizar el sitio web de Flores D&D, usted acepta cumplir con estos términos 
                y condiciones. Si no está de acuerdo con alguna parte, le recomendamos no utilizar nuestros servicios.
            </p>

            <h2>2. Descripción del servicio</h2>
            <p>
                Flores D&D ofrece servicios de floristería incluyendo la venta de arreglos florales, 
                entrega a domicilio y servicios de decoración para eventos.
            </p>

            <h2>3. Productos</h2>
            <p>
                Las imágenes de los productos son representativas. Debido a la naturaleza de las flores, 
                pueden existir variaciones menores en color, tamaño o disponibilidad de especies específicas. 
                Nos reservamos el derecho de sustituir flores por otras de igual o mayor valor.
            </p>

            <h2>4. Precios y pagos</h2>
            <ul>
                <li>Todos los precios están expresados en pesos mexicanos (MXN)</li>
                <li>Los precios incluyen IVA</li>
                <li>Aceptamos pagos por transferencia bancaria y tarjeta de crédito/débito</li>
                <li>El pedido se confirma una vez recibido el pago</li>
            </ul>

            <h2>5. Entregas</h2>
            <ul>
                <li>Entregamos en la zona metropolitana especificada</li>
                <li>Los horarios de entrega son aproximados</li>
                <li>El cliente debe proporcionar datos de contacto precisos</li>
                <li>Si el destinatario no se encuentra, intentaremos contactar al remitente</li>
                <li>Flores D&D no se hace responsable por entregas fallidas debido a información incorrecta</li>
            </ul>

            <h2>6. Cancelaciones y cambios</h2>
            <ul>
                <li>Cancelaciones con 24+ horas de anticipación: reembolso completo</li>
                <li>Cancelaciones con menos de 24 horas: 50% del valor</li>
                <li>Una vez enviado el pedido no se aceptan cancelaciones</li>
                <li>Cambios de dirección sujetos a disponibilidad</li>
            </ul>

            <h2>7. Garantía de frescura</h2>
            <p>
                Garantizamos la frescura de nuestras flores. Si no está satisfecho con la calidad, 
                contáctenos dentro de las primeras 24 horas con evidencia fotográfica y haremos 
                la reposición sin costo.
            </p>

            <h2>8. Limitación de responsabilidad</h2>
            <p>
                Flores D&D no será responsable por daños indirectos, incidentales o consecuentes 
                derivados del uso de nuestros servicios.
            </p>

            <h2>9. Propiedad intelectual</h2>
            <p>
                Todo el contenido del sitio (imágenes, textos, logos, diseños) es propiedad de 
                Flores D&D y está protegido por leyes de propiedad intelectual.
            </p>

            <h2>10. Ley aplicable</h2>
            <p>
                Estos términos se rigen por las leyes de los Estados Unidos Mexicanos. 
                Cualquier disputa será resuelta en los tribunales competentes.
            </p>

            <h2>11. Contacto</h2>
            <p>
                Para cualquier consulta sobre estos términos:
            </p>
            <ul>
                <li>Email: {{ config('flores.email') }}</li>
                <li>Teléfono: {{ config('flores.phone') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
