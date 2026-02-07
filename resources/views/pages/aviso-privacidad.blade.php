@extends('layouts.app')

@section('content')
<div class="min-h-dvh bg-white">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto prose prose-lg">
            <h1 class="font-sans font-bold text-3xl text-primary">Aviso de Privacidad</h1>
            
            <p class="text-dark/70">Última actualización: {{ now()->format('d/m/Y') }}</p>

            <h2>1. Responsable del tratamiento</h2>
            <p>
                <strong>Flores D&D</strong> (en adelante "nosotros" o "la empresa") es responsable del tratamiento 
                de sus datos personales, con domicilio en [Dirección], y puede ser contactado en el correo 
                electrónico {{ config('flores.email') }}.
            </p>

            <h2>2. Datos personales que recopilamos</h2>
            <p>Para la prestación de nuestros servicios, recopilamos los siguientes datos personales:</p>
            <ul>
                <li>Nombre completo</li>
                <li>Dirección de entrega</li>
                <li>Correo electrónico</li>
                <li>Número telefónico</li>
                <li>Datos de pago (procesados de forma segura por terceros)</li>
            </ul>

            <h2>3. Finalidades del tratamiento</h2>
            <p>Sus datos personales serán utilizados para:</p>
            <ul>
                <li>Procesar y entregar sus pedidos</li>
                <li>Enviar confirmaciones y actualizaciones de pedidos</li>
                <li>Contactarle en caso de ser necesario para la entrega</li>
                <li>Enviar comunicaciones promocionales (si lo autoriza)</li>
                <li>Mejorar nuestros servicios</li>
            </ul>

            <h2>4. Seguridad de sus datos</h2>
            <p>
                Implementamos medidas de seguridad técnicas y organizativas para proteger sus datos personales 
                contra acceso no autorizado, pérdida o alteración. Utilizamos cifrado AES-256 para datos sensibles 
                y conexiones HTTPS para todas las comunicaciones.
            </p>

            <h2>5. Derechos ARCO</h2>
            <p>
                Usted tiene derecho a Acceder, Rectificar, Cancelar u Oponerse al tratamiento de sus datos 
                personales. Para ejercer estos derechos, envíe un correo a {{ config('flores.email') }}.
            </p>

            <h2>6. Cookies</h2>
            <p>
                Utilizamos cookies esenciales para el funcionamiento del sitio y cookies analíticas para 
                mejorar la experiencia del usuario. Puede configurar su navegador para rechazar cookies.
            </p>

            <h2>7. Cambios al aviso de privacidad</h2>
            <p>
                Nos reservamos el derecho de modificar este aviso de privacidad. Cualquier cambio será 
                publicado en esta página.
            </p>

            <h2>8. Contacto</h2>
            <p>
                Para cualquier duda o comentario sobre este aviso de privacidad, puede contactarnos en:
            </p>
            <ul>
                <li>Email: {{ config('flores.email') }}</li>
                <li>Teléfono: {{ config('flores.phone') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
